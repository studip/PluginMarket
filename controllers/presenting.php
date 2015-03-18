<?php
require_once 'app/controllers/plugin_controller.php';

class PresentingController extends PluginController {

    protected $last_pluginmarket_visit = null;

    function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem("/pluginmarket/presenting");
        if ($GLOBALS['perm']->have_perm("user")) {
            $config = UserConfig::get($GLOBALS['user']->id);
            $this->last_pluginmarket_visit = $config->getValue("last_pluginmarket_visit") ?: time();
            $_SESSION['last_pluginmarket_visit'] = time();
            $config->store("last_pluginmarket_visit", $_SESSION['last_pluginmarket_visit']);
        }
        PageLayout::addStylesheet($this->plugin->getPluginURL()."/assets/pluginmarket.css");
        
                $statement = DBManager::get()->prepare("
            SELECT pluginmarket_tags.tag, COUNT(*) AS number
            FROM pluginmarket_tags
                INNER JOIN pluginmarket_plugins ON (pluginmarket_plugins.plugin_id = pluginmarket_tags.plugin_id)
            WHERE pluginmarket_tags. proposal = '0'
                AND pluginmarket_plugins.approved = 1
                AND pluginmarket_plugins.publiclyvisible = 1
            GROUP BY pluginmarket_tags.tag
            ORDER BY number DESC, RAND()
            LIMIT 25
        ");
        $statement->execute();
        $this->tags = $statement->fetchAll(PDO::FETCH_ASSOC);

                
        // Sidebar
        $sidebar = Sidebar::Get();
        
        // Create search widget
        $searchWidget = new SearchWidget($this->url_for('presenting/all'));
        $searchWidget->addNeedle(_('Suche'), 'search', true);
        $sidebar->addWidget($searchWidget);
        
        // Create cloud
        $tagWidget = new LinkCloudWidget();
        $tagWidget->setTitle(_("Beliebte Tags"));
        foreach ($this->tags as $tag) {
            $tagWidget->addLink($tag['tag'], $this->url_for('presenting/all', array('tag' => $tag['tag'])), $tag['number']);
        }
        $sidebar->addWidget($tagWidget);
        
    }

    public function overview_action() {
        if ($GLOBALS['perm']->have_perm("user")) {
            if ($this->last_pluginmarket_visit !== time()) {
                $this->new_plugins = MarketPlugin::findBySql("publiclyvisible = 1 AND approved = 1 AND published > ? ORDER BY mkdate DESC", array($this->last_pluginmarket_visit));
            }
        }

        $this->plugins = MarketPlugin::findBySQL("publiclyvisible = 1 AND approved = 1 ORDER BY RAND() LIMIT 6");
    }

    public function all_action() {
        if (Request::get("search")) {
            $this->plugins = MarketPlugin::findBySQL("
                    (
                        name LIKE :likesearch
                        OR (SELECT CONCAT(Vorname, ' ', Nachname) FROM auth_user_md5 WHERE user_id = pluginmarket_plugins.user_id LIMIT 1) LIKE :likesearch
                        OR MATCH (short_description, description) AGAINST (:search IN BOOLEAN MODE)
                        OR (SELECT GROUP_CONCAT(' ', tag) FROM pluginmarket_tags WHERE pluginmarket_tags.plugin_id = plugin_id GROUP BY pluginmarket_tags.plugin_id LIMIT 1) LIKE :likesearch
                    )
                    AND publiclyvisible = 1
                    AND approved = 1
                ORDER BY (IF(name LIKE :likesearch, 6, 0) + MATCH (short_description, description) AGAINST (:search)) ", array(
                    'likesearch' => "%".Request::get("search")."%",
                    'search' => Request::get("search")
                )
            );
        } elseif(Request::get("tag")) {
            $statement = DBManager::get()->prepare("
                SELECT pluginmarket_plugins.*
                FROM pluginmarket_plugins
                    INNER JOIN pluginmarket_tags ON (pluginmarket_plugins.plugin_id = pluginmarket_tags.plugin_id)
                WHERE pluginmarket_tags.tag = :tag
                    AND pluginmarket_plugins.approved = 1
                    AND pluginmarket_plugins.publiclyvisible = 1
            ");
            $statement->execute(array('tag' => Request::get("tag")));
            $plugin_data = $statement->fetchAll(PDO::FETCH_ASSOC);
            $this->plugins = array();
            foreach ($plugin_data as $data) {
                $plugin = new MarketPlugin();
                $plugin->setData($data);
                $plugin->setNew(false);
                $this->plugins[] = $plugin;
            }
        } else {
            $this->plugins = MarketPlugin::findBySQL("publiclyvisible = 1 AND approved = 1 ORDER BY name ASC");
        }
    }

    public function details_action($plugin_id) {
        Navigation::addItem('/pluginmarket/presenting/details', new AutoNavigation(_('Details'), $this->url_for('presenting/details/'.$plugin_id)));
        $this->marketplugin = new MarketPlugin($plugin_id);
        if (Request::isPost() && Request::submitted("delete_plugin") && $this->marketplugin->isRootable()) {
            $this->marketplugin->delete();
            PageLayout::postMessage(MessageBox::success(_("Plugin wurde gelöscht.")));
            $this->redirect("pluginmarket/presenting/overview");
        }

    }

    public function review_action($plugin_id) {
        $reviews = MarketReview::findBySQL("plugin_id = ? AND user_id = ?", array($plugin_id, $GLOBALS['user']->id));
        if (count($reviews)) {
            $this->review = $reviews[0];
        } else {
            $this->review = new MarketReview();
            $this->review['plugin_id'] = $plugin_id;
            $this->review['user_id'] = $GLOBALS['user']->id;
        }
        if (Request::isXhr()) {
            $this->response->add_header('X-Title', $this->review->isNew() ? _("Plugin reviewen") : _("Review bearbeiten"));
            $this->set_layout(null);
            $this->set_content_type('text/html;charset=windows-1252');
        }
    }

    public function save_review_action($plugin_id) {
        if (!Request::isPost()) {
            throw new Exception("Wrong method, use POST.");
        }
        $this->marketplugin = MarketPlugin::find($plugin_id);
        if (!$this->marketplugin) {
            throw new Exception("Unknown plugin.");
        }
        $reviews = MarketReview::findBySQL("plugin_id = ? AND user_id = ?", array($plugin_id, $GLOBALS['user']->id));
        if (count($reviews)) {
            $this->review = $reviews[0];
        } else {
            $this->review = new MarketReview();
            $this->review['plugin_id'] = $plugin_id;
            $this->review['user_id'] = $GLOBALS['user']->id;
        }
        $data = Request::getArray("data");
        $this->review['review'] = trim($data['review']) ?: null;
        if ($data['rating'] <= 5 && $data['rating'] >= 0) {
            $this->review['rating'] = $data['rating'];
        } else {
            throw new Exception("Rating is not in accepted range.");
        }
        $this->review->store();

        PersonalNotifications::add(
            $this->marketplugin['user_id'],
            PluginEngine::getURL($this->plugin, array(), "presenting/details/".$plugin_id),
            sprintf(_("Ihr Plugin %s wurde von %s bewertet."), $this->marketplugin['name'], get_fullname($GLOBALS['user']->id)),
            null,
            Assets::image_path("icons/blue/star.svg")
        );

        PageLayout::postMessage(MessageBox::success(_("Review/Bewertung wurde gespeichert.")));
        $this->redirect("pluginmarket/presenting/details/".$plugin_id);
    }

    public function download_action($release) {
        $release = new MarketRelease($release);
        $release->outputZip();
        $release['downloads'] += 1;
        $release->store();
        $this->render_nothing();
    }

    public function image_action($image_id) {
        $this->image = new MarketImage($image_id);

        $this->set_content_type($this->image['mimetype']);
        $this->image->outputImage();

        $this->render_nothing();
    }

    public function follow_release_action($release_id) {
        $this->release = new MarketRelease($release_id);
        $this->following = MarketReleaseFollower::findByUserAndRelease($GLOBALS['user']->id, $release_id);

        if (Request::isPost()) {
            if (!$this->following) {
                $this->following = new MarketReleaseFollower();
                $this->following['user_id'] = $GLOBALS['user']->id;
                $this->following['release_id'] = $release_id;
            }
            $this->following['url'] = Request::get("url");
            $this->following['security_token'] = Request::get("security_token") ? Request::get("security_token") : null;
            $this->following->store();
            PageLayout::postMessage(MessageBox::success(_("Daten wurden gespeichert.")));
        }

        if (Request::isXhr()) {
            $this->response->add_header('X-Title', sprintf(_('Automatisches Update für "%s" einrichten'), $this->release->plugin['name']." ".$this->release['version']));
            $this->set_layout(null);
            $this->set_content_type('text/html;charset=windows-1252');
        }
    }

    public function register_for_pluginnews_action($plugin_id) {
        $this->marketplugin = MarketPlugin::find($plugin_id);
        if (Request::isPost()) {
            if (Request::submitted("follow")) {
                $following = new MarketPluginFollower();
                $following['plugin_id'] = $plugin_id;
                $following['user_id'] = $GLOBALS['user']->id;
                $following->store();
                PageLayout::postMessage(MessageBox::success(_("Sie bekommen nun Informationen zu Updates dieses Plugins zugeschickt.")));
            } elseif(Request::submitted("unfollow")) {
                $following = MarketPluginFollower::findByUserAndPlugin($GLOBALS['user']->id, $plugin_id);
                $following->delete();
                PageLayout::postMessage(MessageBox::success(_("Sie werden jetzt keine weiteren Neuigkeiten über dieses Plugin als Stud.IP Nachricht bekommen.")));
            }
        }

        if (Request::isXhr()) {
            $this->response->add_header('X-Title', _("Plugin abonnieren"));
            $this->set_layout(null);
            $this->set_content_type('text/html;charset=windows-1252');
        }
    }


}