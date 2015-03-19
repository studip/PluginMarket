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
            $xml_plugin = $xml_plugins->appendChild($doc->createElement('plugin'));
            $xml_plugin->setAttribute('name', $this->xml_ready($plugin['name']));
            $xml_plugin->setAttribute('homepage', $plugin['url']);
            $xml_plugin->setAttribute('short_description', $this->xml_ready($plugin['short_description']));
            $xml_plugin->setAttribute('description', $this->xml_ready($plugin['description']));
            $xml_plugin->setAttribute('image', $plugin->getLogoURL(true));
            $xml_plugin->setAttribute('score', $plugin->getRating());
            foreach ($plugin->releases as $release) {
                $xml_release = $xml_plugin->appendChild($doc->createElement('release'));
                $xml_release->setAttribute('version', $release['version']);
                $xml_release->setAttribute('studipMinVersion', $release['studip_min_version']);
                $xml_release->setAttribute('studipMaxVersion', $release['studip_max_version']);
                $xml_release->setAttribute('url', $this->absolute_url_for('presenting/download/' . $release->getId()));
            }
        }

        $this->response->add_header('Content-Type', 'text/xml;charset=UTF-8');
        $this->render_text($doc->saveXML());
    }

    /**
     * Converts a given string to our xml friendly text.
     * This step involves purifying the string 
     */
    public function xml_ready($string)
    {
        static $purifier = null;
        static $fixer = null;
        static $markdown = null;

        if ($purifier === null) {
            $purifier_config = HTMLPurifier_Config::createDefault();
            $purifier_config->set('Cache.SerializerPath', realpath($GLOBALS['TMP_PATH']));
            $purifier = new HTMLPurifier($purifier_config);

            $markdown = new HTML_To_Markdown();
            $markdown->set_option('strip_tags', true);
        }

        $string = studip_utf8encode($string);
        $string = str_replace('&nbsp;', ' ', $string);

        $string = $purifier->purify($string);
        $string = $markdown->convert($string);

        $string = preg_replace('/\[\]\((\w+:\/\/.*?)\)/', '', $string);

        $string = preg_replace('/\[(\w+:\/\/.*?)\/?\]\(\\1\/?\s+"(.*?)"\)/isxm', '$2: $1', $string);
        $string = preg_replace('/\[(\w+:\/\/.*?)\/?\]\(\\1\/?\)/isxm', '$1', $string);
        $string = preg_replace('/\[(.*?)\]\((\w+:\/\/.*?)\)/', '$1: $2', $string);

        $string = trim($string);

        return $string;
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