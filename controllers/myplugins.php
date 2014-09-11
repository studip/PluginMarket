<?php
require_once 'app/controllers/plugin_controller.php';

class MypluginsController extends PluginController {

    function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem("/pluginmarket/myplugins");
        $this->set_content_type('text/html;charset=windows-1252');
    }

    public function overview_action()
    {
        $this->plugins = MarketPlugin::findBySQL("1=1");
    }

    public function add_action() {
        $this->marketplugin = new MarketPlugin();
        if (Request::isXhr()) {
            $this->set_layout(null);
        }
        $this->render_action("edit");
    }

    public function edit_action($plugin_id) {
        $this->marketplugin = new MarketPlugin($plugin_id);
    }

    public function save_action() {
        if (!Request::isPost()) {
            throw new Exception("Method not allowed. Try a POST request.");
        }
        $this->marketplugin = new MarketPlugin(Request::option("id") ?: null);
        $this->marketplugin->setData(Request::getArray("data"));
        if ($this->marketplugin->isNew()) {
            $this->marketplugin['user_id'] = $GLOBALS['user']->id;
        }
        $this->marketplugin->store();
        PageLayout::postMessage(MessageBox::success(_("Plugin wurde gespeichert.")));
        $this->redirect("pluginmarket/presenting/details/".$this->marketplugin->getId());
    }

}