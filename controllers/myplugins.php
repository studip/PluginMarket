<?php
require_once 'app/controllers/plugin_controller.php';

class MypluginsController extends PluginController {

    function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem("/pluginmarket/myplugins");
    }

    public function overview_action()
    {
        $this->plugins = MarketPlugin::findBySQL("1=1");
    }

}