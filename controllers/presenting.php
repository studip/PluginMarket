<?php
require_once 'app/controllers/plugin_controller.php';

class PresentingController extends PluginController {

    function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem("/pluginmarket/presenting");
        if ($GLOBALS['perm']->have_perm("user")) {
            object_set_visit(get_class($this->plugin), "plugin");
        }
        PageLayout::addStylesheet($this->plugin->getPluginURL()."/assets/pluginmarket.css");
    }

    public function overview_action() {

        if ($GLOBALS['perm']->have_perm("user")) {
            $last_visit = object_get_visit(get_class($this->plugin), "plugin");
            if ($last_visit !== false) {
                $this->new_plugins = MarketPlugin::findBySql("publiclyvisible = 1 AND approved = 1 AND mkdate > ? ORDER BY mkdate DESC", array($last_visit));
            }
        }

        $this->plugins = MarketPlugin::findBySQL("publiclyvisible = 1 AND approved = 1 ORDER BY name ASC");
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