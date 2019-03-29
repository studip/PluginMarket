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
        $doc = new DomDocument('1.0', 'utf-8');
        $doc->formatOutput = true;
        $doc->encoding = 'utf-8';
        $xml_plugins = $doc->appendChild($doc->createElement('plugins'));

        $plugins = MarketPlugin::findBySQL("publiclyvisible = 1 AND approved = 1 ORDER BY name ASC");
        foreach ($plugins as $plugin) {
            $xml_plugin = $xml_plugins->appendChild($this->create_xml_element($doc, 'plugin', null, [
                'displayname'       => $plugin->name,
                'name'              => $plugin->pluginname,
                'homepage'          => $plugin->url,
                'short_description' => $plugin->short_description,
                'description'       => $plugin->description,
                'image'             => $plugin->getLogoURL(true),
                'score'             => $plugin['rating'],
                'marketplace_url'   => $this->absolute_url_for("presenting/details/{$plugin->id}"),
            ]));
            foreach ($plugin->releases as $release) {
                $xml_plugin->appendChild($this->create_xml_element($doc, 'release', null, [
                    'version'          => $release->version,
                    'studipMinVersion' => $release->studip_min_version,
                    'studipMaxVersion' => $release->studip_max_version,
                    'url'              => $this->absolute_url_for('presenting/download/' . $release->id),
                ]));
            }
        }

        $this->set_content_type('text/xml;charset=UTF-8');
        $this->render_text($doc->saveXML());
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
