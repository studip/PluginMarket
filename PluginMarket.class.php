<?php

require_once __DIR__."/classes/MarketPlugin.class.php";

class PluginMarket extends StudIPPlugin implements SystemPlugin {

    public function __construct() {
        parent::__construct();
        $top = new Navigation($this->getDisplayTitle(), PluginEngine::getURL($this, array(), "presenting/overview"));
        $top->setImage($this->getPluginURL()."/assets/topicon_42.png");
        $top->addSubNavigation("presenting", new Navigation($this->getDisplayTitle(), PluginEngine::getURL($this, array(), "presenting/overview")));
        if ($GLOBALS['perm']->have_perm("autor")) {
            $top->addSubNavigation("myplugins", new Navigation(_("Meine Plugins"), PluginEngine::getURL($this, array(), "myplugins/overview")));
        }
        Navigation::addItem("/pluginmarket", $top);
    }

    public function getDisplayTitle() {
        return _("PluginMarktplatz");
    }

}