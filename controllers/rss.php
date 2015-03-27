<?php
require_once 'market_controller.php';

class RssController extends MarketController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $this->set_content_type('application/rss+xml;charset=UTF-8');
        $this->set_layout(false);
    }

    /**
     * Renders an rss feed containing the newest plugins.
     *
     * @param int $limit How many plugins to display (optional, default 20)
     */
    public function newest_action($limit = 20)
    {
        $doc = new DomDocument('1.0', 'utf-8');
        $doc->formatOutput = true;
        $doc->encoding = 'utf-8';
        $rss = $doc->appendChild($this->create_xml_element($doc, 'rss', null, array(
            'version'    => '2.0',
            'xmlns:atom' => 'http://www.w3.org/2005/Atom',
        )));
        
        $channel = $rss->appendChild($doc->createElement('channel'));
        $channel->appendChild($this->create_xml_element($doc, 'title', 'Stud.IP Plugin Marktplatz - Neueste Plugins'));
        $channel->appendChild($this->create_xml_element($doc, 'description', 'Liste der neuesten Plugins auf dem Stud.IP Plugin Marktplatz'));
        $channel->appendChild($this->create_xml_element($doc, 'link', 'http://plugins.studip.de'));
        $channel->appendChild($this->create_xml_element($doc, 'lastBuildDate', gmdate('D, d M Y H:i:s T')));
        $channel->appendChild($this->create_xml_element($doc, 'generator', _('Stud.IP Plugin Marktplatz')));
        $channel->appendChild($this->create_xml_element($doc, 'atom:link', null, array(
            'rel'  => 'self',
            'type' => 'application/rss+xml',
            'href' => $this->absolute_url_for('rss/newest'),
        )));

        $plugins = MarketPlugin::findBySQL("publiclyvisible = 1 AND approved = 1 ORDER BY mkdate DESC");
        foreach ($plugins as $plugin) {
            if (count($plugin->releases) === 0) {
                continue;
            }

            $rss_plugin = $channel->appendChild($doc->createElement('item'));
            $rss_plugin->appendChild($this->create_xml_element($doc, 'title', $plugin->name));
            $rss_plugin->appendChild($this->create_xml_element($doc, 'link', $this->absolute_url_for('presenting/details/' . $plugin->id)));
            $rss_plugin->appendChild($this->create_xml_element($doc, 'guid', $this->absolute_url_for('presenting/details/' . $plugin->id), array(
                'isPermaLink' => 'true'
            )));
            $rss_plugin->appendChild($this->create_xml_element($doc, 'description', $plugin->description, array(), false));
            if ($plugin->user) {
                $rss_plugin->appendChild($this->create_xml_element($doc, 'author', $plugin->user->email . ' (' . $plugin->user->getFullname() . ')'));
            }
        }

        $this->render_text($doc->saveXML());
    }
}