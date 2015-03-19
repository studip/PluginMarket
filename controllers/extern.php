<?php
require_once 'market_controller.php';

class ExternController extends MarketController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $this->set_layout(null);
    }

    public function xml_action()
    {
        $this->plugins = MarketPlugin::findBySQL("publiclyvisible = 1 AND approved = 1 ORDER BY name ASC");
        $this->response->add_header('Content-Type', "text/xml");
    }

    public function find_releases_action()
    {
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
                            'version'      => $release['version'],
                            'html_url'     => $this->url_for('presenting/details/' . $plugin->getId()),
                            'download_url' => $this->url_for('presenting/download/' . $release->getId()),
                        );
                    }
                }
            }
        }
        $this->render_json($output);
    }
}