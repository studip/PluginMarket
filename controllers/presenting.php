<?php
require_once 'app/controllers/plugin_controller.php';

class PresentingController extends PluginController {

    function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem("/pluginmarket/presenting");
    }

    public function overview_action() {
        $this->plugins = MarketPlugin::findBySQL("1=1");
    }

    public function details_action($plugin_id) {
        $this->marketplugin = new MarketPlugin($plugin_id);
    }

}