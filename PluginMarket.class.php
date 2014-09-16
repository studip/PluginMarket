<?php

require_once __DIR__."/classes/MarketPlugin.class.php";
require_once __DIR__."/classes/MarketRelease.class.php";
require_once __DIR__."/classes/MarketImage.class.php";

class PluginMarket extends StudIPPlugin implements SystemPlugin {

    public function __construct() {
        parent::__construct();
        $top = new Navigation($this->getDisplayTitle(), PluginEngine::getURL($this, array(), "presenting/overview"));
        $top->setImage($this->getPluginURL()."/assets/topicon_".($GLOBALS['auth']->auth['devicePixelRatio'] ? 84 : 42).".png");
        $top->addSubNavigation("presenting", new Navigation($this->getDisplayTitle(), PluginEngine::getURL($this, array(), "presenting/overview")));
        if ($GLOBALS['perm']->have_perm("autor")) {
            $top->addSubNavigation("myplugins", new Navigation(_("Meine Plugins"), PluginEngine::getURL($this, array(), "myplugins/overview")));
        }
        if ($GLOBALS['perm']->have_perm("user")) {
            $last_visit = object_get_visit(get_class($this), "plugin");
            if ($last_visit !== false) {
                $badge_number = MarketPlugin::countBySql("mkdate > ?", array($last_visit));
                if ($badge_number > 0) {
                    $top->setBadgeNumber($badge_number);
                }
            }
        }
        if ($GLOBALS['perm']->have_perm("root")) {
            $approving = new Navigation(_("Qualitätssicherung"), PluginEngine::getURL($this, array(), "approving/overview"));
            $top->addSubNavigation("approving", $approving);
        }
        Navigation::addItem("/pluginmarket", $top);

        $loginlink = new Navigation($this->getDisplayTitle(), PluginEngine::getURL($this, array(), "presenting/overview"));
        $loginlink->setDescription(_("Laden Sie hier Plugins für Ihr Stud.IP herunter"));
        Navigation::addItem("/login/pluginmarket",$loginlink);
    }

    public function getDisplayTitle() {
        return _("PluginMarktplatz");
    }

}