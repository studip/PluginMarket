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
    }

    public function overview_action() {

        if ($GLOBALS['perm']->have_perm("user")) {
            $last_visit = object_get_visit(get_class($this->plugin), "plugin");
            if ($last_visit !== false) {
                $this->new_plugins = MarketPlugin::findBySql("mkdate > ? ORDER BY mkdate DESC", array($last_visit));
            }
        }

        $this->plugins = MarketPlugin::findBySQL("publiclyvisible = 1 AND approved = 1 ORDER BY name ASC");
    }

    public function details_action($plugin_id) {
        $this->marketplugin = new MarketPlugin($plugin_id);
    }

    public function download_action($release) {
        $release = new MarketRelease($release);
        $release->outputZip();
        $release['downloads'] += 1;
        $release->store();
        $this->render_nothing();
    }

}