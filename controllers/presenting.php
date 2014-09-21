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
    }

    public function overview_action() {
        if ($GLOBALS['perm']->have_perm("user")) {
            if ($this->last_pluginmarket_visit !== time()) {
                $this->new_plugins = MarketPlugin::findBySql("publiclyvisible = 1 AND approved = 1 AND published > ? ORDER BY mkdate DESC", array($this->last_pluginmarket_visit));
            }
        }

        if (Request::get("search")) {
            $this->plugins = MarketPlugin::findBySQL("
                    (
                        name LIKE :likesearch
                        OR MATCH (short_description, description) AGAINST (:search IN BOOLEAN MODE)
                    )
                    AND publiclyvisible = 1
                    AND approved = 1
                ORDER BY (IF(name LIKE :likesearch, 6, 0) + MATCH (short_description, description) AGAINST (:search)) ", array(
                    'likesearch' => "%".Request::get("search")."%",
                    'search' => Request::get("search")
                )
            );
        } else {
            $this->plugins = MarketPlugin::findBySQL("publiclyvisible = 1 AND approved = 1 ORDER BY name ASC");
        }
    }

    public function details_action($plugin_id) {
        $this->marketplugin = new MarketPlugin($plugin_id);
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
        }
    }

    public function save_review_action($plugin_id) {
        if (!Request::isPost()) {
            throw new Exception("Wrong method, use POST.");
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
        $this->image->outputImage();
        $this->render_nothing();
    }


}