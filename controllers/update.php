<?php
require_once 'app/controllers/plugin_controller.php';

class UpdateController extends PluginController {

    public function release_action($release_id) {
        $release = new MarketRelease($release_id);
        if ($release->isNew()) {
            throw new Exception("Unknown release.");
        }
        if (!$release['repository_download_url']) {
            //might happen more often than we think, so we better be polite and die.
            echo "Nothing to do.";
            die();
        }
        if ($release->getSecurityHash() === Request::get("s")) {
            $release->installFile();
            $this->render_text("OK");
        } else {
            $this->render_text("Insecure request.");
        }
    }

}