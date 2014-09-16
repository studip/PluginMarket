<?php
require_once 'app/controllers/plugin_controller.php';

class ApprovingController extends PluginController {

    function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        if (!$GLOBALS['perm']->have_perm("root")) {
            throw new AcessDeniedException("Kein Zutritt");
        }

        Navigation::activateItem("/pluginmarket/approving");
        $this->set_content_type('text/html;charset=windows-1252');
        PageLayout::addStylesheet($this->plugin->getPluginURL()."/assets/pluginmarket.css");
    }

    public function overview_action()
    {
        $this->plugins = MarketPlugin::findBySQL("approved = 0 AND publiclyvisible = 1 ORDER BY mkdate DESC");
    }

    public function review_action($plugin_id) {
        $this->marketplugin = new MarketPlugin($plugin_id);
        if ($this->marketplugin['approved']) {
            throw new Exception("Plugin ist schon reviewt.");
        }
        if (Request::isXhr()) {
            $this->response->add_header('X-Title', _("Review schreiben"));
            $this->set_layout(null);
        }
    }

    public function approve_action($plugin_id) {
        $this->marketplugin = new MarketPlugin($plugin_id);
        if ($this->marketplugin['approved']) {
            throw new Exception("Plugin ist schon reviewt.");
        }
        $this->marketplugin['approved'] = (int) Request::int("approved");
        if (!$this->marketplugin['approved']) {
            $this->marketplugin['publiclyvisible'] = 0;
        }
        $this->marketplugin->store();

        $messaging = new messaging();
        $messaging->insert_message(
            sprintf(_("Ihr Plugin %s wurde reviewt:"), $this->marketplugin['name'])
                ."\n\n"
                .($this->marketplugin['approved'] ? _("Es ist in den Marktplatz aufgenommen worden!") : _("Es ist leider noch nicht in den Marktplatz aufgenommen."))
                ."\n\n"
                .(Request::get("review") ? _("Begründung:")."\n\n".Request::get("review") : _("Ein ausführliches Review wurde nicht angegeben und muss bei Bedarf direkt angefragt werden.")),
            get_username($this->marketplugin['user_id']),
            '',
            '',
            '',
            '',
            '',
            _("Pluginreview"),
            true,
            'normal',
            "pluginreview"
        );

        PageLayout::postMessage(MessageBox::success(_("Review wurde gespeichert.")));
        $this->redirect("pluginmarket/approving/overview");
    }

}