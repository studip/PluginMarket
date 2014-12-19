<?php
require_once 'app/controllers/plugin_controller.php';

class ExternController extends PluginController
{
    public function xml_action() {
        $this->plugins = MarketPlugin::findBySQL("publiclyvisible = 1 AND approved = 1 ORDER BY name ASC");
        $this->set_layout(null);
        $this->response->add_header('Content-Type', "text/xml");
    }

    public function find_releases_action() {
        $output = array();
        $studipversion = Request::get("studipversion");
        $plugins = MarketPlugin::findByPluginclassname(Request::get("classname"));
        if (!count($plugins)) {
            $output['info'] = "No release found in the market.";
        } else {
            foreach ($plugins as $plugin) {
                foreach ($plugin->releases as $release) {
                    if ((!$release['studip_min_version'] || version_compare($studipversion, $release['studip_min_version'], ">="))
                            && (!$release['studip_max_version'] || version_compare($studipversion, $release['studip_max_version'], "<="))) {
                        $output['releases'][] = array(
                            'version' => $release['version'],
                            'html_url' => PluginEngine::getURL($this->plugin, array(), "presenting/details/".$plugin->getId()),
                            'download_url' => PluginEngine::getURL($this->plugin, array(), "presenting/download/".$release->getId())
                        );
                    }
                }
            }
        }
        $this->render_json($output);
    }
}