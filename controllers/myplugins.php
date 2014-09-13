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
        if (Request::isXhr()) {
            $this->response->add_header('X-Title', _("Plugin bearbeiten"));
            $this->set_layout(null);
        }
    }

    public function edit_release_action($release_id) {
        $this->release = new MarketRelease($release_id);
        if (Request::isXhr()) {
            $this->response->add_header('X-Title', _("Release bearbeiten"));
            $this->set_layout(null);
        }
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
        $release_data = Request::getArray("release");
        if ($release_data['type'] !== "zipfile" || $_FILES['release_file']['tmp_path']) {
            $release = new MarketRelease();
            $release->setData($release_data);
            $release['plugin_id'] = $this->marketplugin->getId();
            $release['user_id'] = $GLOBALS['user']->id;
            $release->installFile();
            $release->store();
        }
        PageLayout::postMessage(MessageBox::success(_("Plugin wurde gespeichert.")));
        $this->redirect("pluginmarket/presenting/details/".$this->marketplugin->getId());
    }

    public function save_release_action() {
        if (!Request::isPost()) {
            throw new Exception("Method not allowed. Try a POST request.");
        }
        $this->release = new MarketRelease(Request::option("id"));
        $release_data = Request::getArray("release");
        $this->release->setData($release_data);
        if ($release_data['type'] === "zipfile") {
            $this->release['repository_download_url'] = null;
        }
        $this->release->installFile();
        $this->release->store();

        PageLayout::postMessage(MessageBox::success(_("Release wurde gespeichert.")));
        $this->redirect("pluginmarket/presenting/details/".$this->release->plugin->getId());
    }

}