<?php
require_once 'market_controller.php';

class MypluginsController extends MarketController
{
    function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        Navigation::activateItem("/pluginmarket/myplugins");
        $this->set_content_type('text/html;charset=windows-1252');
    }

    public function overview_action()
    {
        $this->plugins = MarketPlugin::findBySQL("LEFT JOIN pluginmarket_user_plugins USING (plugin_id) 
            WHERE pluginmarket_plugins.user_id = :user_id 
                OR pluginmarket_user_plugins.user_id = :user_id
            GROUP BY pluginmarket_plugins.plugin_id
            ORDER BY mkdate DESC", array('user_id' => $GLOBALS['user']->id)
        );
    }

    public function addfromzip_action()
    {
        CSRFProtection::verifyUnsafeRequest();
        if (isset($_FILES['release_file']['tmp_name'])) {
            $plugin = new MarketPlugin();
            $plugin->setId($plugin->getNewId());
            $plugin->user_id = $GLOBALS['user']->id;
            $release = new MarketRelease();
            $release->plugin = $plugin;
            $release['user_id'] = $GLOBALS['user']->id;
            //throws Exception on error
            $release->installFile();
            $this->redirect($this->url_for('/overview', ['edit_plugin_id' => $plugin->id]));
        }
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

    public function add_release_action($plugin_id) {
        $this->marketplugin = new MarketPlugin($plugin_id);
        $this->release = new MarketRelease();
        if (Request::isXhr()) {
            $this->response->add_header('X-Title', _("Release hinzufügen"));
            $this->set_layout(null);
        }
        $this->render_action("edit_release");
    }

    public function edit_release_action($release_id) {
        $this->release = new MarketRelease($release_id);
        $this->marketplugin = $this->release->plugin;
        if (!$this->marketplugin->isNew() && !$this->marketplugin->isWritable()) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        if (Request::isXhr()) {
            $this->response->add_header('X-Title', _("Release bearbeiten"));
            $this->set_layout(null);
        }
    }

    public function edit_images_action($plugin_id) {
        $this->marketplugin = new MarketPlugin($plugin_id);
        if (Request::isXhr()) {
            $this->response->add_header('X-Title', _("Galerie bearbeiten"));
            $this->set_layout(null);
        }
    }

    public function save_action() {
        if (!Request::isPost()) {
            throw new Exception("Method not allowed. Try a POST request.");
        }
        $this->marketplugin = new MarketPlugin(Request::option("id") ?: null);
        if (!$this->marketplugin->isNew() && !$this->marketplugin->isWritable()) {
            throw new AccessDeniedException("Kein Zugriff");
        }

        if (Request::submitted("data")) {
            $data = Request::getArray("data");
            if (!isset($data["publiclyvisible"])) {
                $data['publiclyvisible'] = 0;
            }
            if (!isset($data["donationsaccepted"])) {
                $data['donationsaccepted'] = 0;
            }
            if (!$this->marketplugin->isRootable() && isset($data['deprecated'])) {
                unset($data['deprecated']);
            }
            if ($this->marketplugin->isRootable() && !isset($data['deprecated'])) {
                $data['deprecated'] = 0;
            }
            $this->marketplugin->setData($data);
            if ($this->marketplugin->isNew() && (MarketPlugin::findOneByPluginname($this->marketplugin->pluginname) || !strlen(trim($this->marketplugin->pluginname)))) {
                PageLayout::postError(_("Ein Plugin mit diesem Namen ist schon im Marktplatz vorhanden!"));
                return $this->redirect($this->url_for('/overview'));
            }
            if ($this->marketplugin->isNew()) {
                $this->marketplugin['user_id'] = $GLOBALS['user']->id;
            }
        }

        $this->marketplugin->store();
        $this->marketplugin->setTags(array_map("trim", explode(",", Request::get("tags"))));

        if (Request::submitted("image_order")) {
            $order = array_flip(Request::getArray("image_order"));
            foreach ($this->marketplugin->images as $image) {
                $image['position'] = $order[$image->getId()] + 1;
                $image->store();
            }
        }

        if (Request::submitted("delete_image")) {
            foreach (Request::getArray("delete_image") as $image_id) {
                MarketImage::find($image_id)->delete();
            }
        }

        if (Request::submitted("edit_images")) {
            $files = $_FILES['new_images'];
            $position = count($this->marketplugin->images);
            foreach ($files['name'] as $index => $name) {
                if ($files['size'][$index]) {
                    $position++;
                    $file = new MarketImage();
                    $file['plugin_id'] = $this->marketplugin->getId();
                    $file['filename'] = $name;
                    $file['mimetype'] = mime_content_type($files['tmp_name'][$index]);
                    $file['position'] = $position;
                    $file->installFromPath($files['tmp_name'][$index]);
                    $file->store();
                }
            }
        }

        if (Request::submitted("release")) {
            $release_data = Request::getArray("release");
            if ($release_data['type'] !== "zipfile" || $_FILES['release_file']['tmp_name']) {
                $release = new MarketRelease();
                if (!isset($release_data['repository_overwrites_descriptionfrom'])) {
                    $release_data['repository_overwrites_descriptionfrom'] = 0;
                }
                $release->setData($release_data);
                $release['plugin_id'] = $this->marketplugin->getId();
                $release['user_id'] = $GLOBALS['user']->id;
                try {
                    $release->installFile();
                } catch (PluginInstallationException $e) {
                    PageLayout::postError($e->getMessage());

                    return $this->redirect($this->url_for('/overview'));
                }
            }

        }

        foreach (Request::getArray("collaborator") as $user_id) {
            if ($this->marketplugin['user_id'] !== $user_id) {
                $statement = DBManager::get()->prepare("
                    INSERT IGNORE INTO pluginmarket_user_plugins
                    SET user_id = :user_id,
                        plugin_id = :plugin_id
                ");
                $statement->execute(array(
                    'user_id' => $user_id,
                    'plugin_id' => $this->marketplugin->getId()
                ));
            }
        }
        $this->marketplugin->store();
        foreach (Request::getArray("drop_collaborator") as $user_id) {
            if ($this->marketplugin['user_id'] === $user_id) {
                if (count($this->marketplugin->more_users)) {
                    $new_boss = $this->marketplugin->more_users[0];
                    $this->marketplugin['user_id'] = $new_boss->getId();
                    $this->marketplugin->store();
                    $statement = DBManager::get()->prepare("
                        DELETE FROM pluginmarket_user_plugins
                        WHERE user_id = :user_id
                            AND plugin_id = :plugin_id
                    ");
                    $statement->execute(array(
                        'user_id' => $new_boss->getId(),
                        'plugin_id' => $this->marketplugin->getId()
                    ));
                }
            } else {
                $statement = DBManager::get()->prepare("
                    DELETE FROM pluginmarket_user_plugins
                    WHERE user_id = :user_id
                        AND plugin_id = :plugin_id
                ");
                $statement->execute(array(
                    'user_id' => $user_id,
                    'plugin_id' => $this->marketplugin->getId()
                ));
            }
        }


        PageLayout::postMessage(MessageBox::success(_("Plugin wurde gespeichert.")));
        $this->redirect('presenting/details/' . $this->marketplugin->getId());
    }

    public function save_release_action() {
        if (!Request::isPost()) {
            throw new Exception("Method not allowed. Try a POST request.");
        }
        $this->release = new MarketRelease(Request::option("id"));
        $this->release['plugin_id'] = Request::option("plugin_id");
        $this->release['user_id'] = $GLOBALS['user']->id;
        $this->marketplugin = new MarketPlugin(Request::option("plugin_id") ?: null);
        if (!$this->marketplugin->isNew() && !$this->marketplugin->isWritable()) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        $release_data = Request::getArray("release");
        $this->release->setData($release_data);
        if ($release_data['type'] === "zipfile") {
            $this->release['repository_download_url'] = null;
        }
        if (!Request::get("use_secret")) {
            $this->release['repository_secret'] = null;
        } elseif(!$this->release['repository_secret']) {
            $this->release['repository_secret'] = md5(uniqid());
        }
        $this->release->installFile();

        $this->release->store();

        PageLayout::postMessage(MessageBox::success(_("Release wurde gespeichert.")));
        $this->redirect('presenting/details/' . $this->release->plugin->getId());
    }


    public function delete_action($plugin_id) {
        $this->marketplugin = MarketPlugin::find($plugin_id);
        if (Request::submitted('delete') && $this->marketplugin->isWritable()) {
            CSRFProtection::verifyUnsafeRequest();
            $this->marketplugin->delete();
            $this->redirect('myplugins/overview');
        }
    }

    public function add_user_action()
    {
        $this->user = User::find(Request::option("user_id"));
        $this->render_template("myplugins/_collaborator.php");
    }


}