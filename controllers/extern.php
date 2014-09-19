<?php
require_once 'app/controllers/plugin_controller.php';

class ExternController extends PluginController
{
    public function xml_action() {
        $this->plugins = MarketPlugin::findBySQL("publiclyvisible = 1 AND approved = 1 ORDER BY name ASC");
        $this->set_layout(null);
        header("Content-Type: text/xml");
    }
}