<?php

require_once __DIR__."/classes/MarketPlugin.class.php";
require_once __DIR__."/classes/MarketRelease.class.php";
require_once __DIR__."/classes/MarketImage.class.php";
require_once __DIR__."/classes/MarketVote.class.php";

class PluginMarket extends StudIPPlugin implements SystemPlugin {

    static protected $studip_domain = null;

    public function __construct() {
        parent::__construct();
        $top = new Navigation($this->getDisplayTitle(), PluginEngine::getURL($this, array(), "presenting/overview"));
        $top->setImage($this->getPluginURL()."/assets/topicon_".($GLOBALS['auth']->auth['devicePixelRatio'] ? 84 : 42).".png");
        $top->addSubNavigation("presenting", new Navigation($this->getDisplayTitle(), PluginEngine::getURL($this, array(), "presenting/overview")));
        if ($GLOBALS['perm']->have_perm("autor")) {
            $top->addSubNavigation("myplugins", new Navigation(_("Meine Plugins"), PluginEngine::getURL($this, array(), "myplugins/overview")));
        }
        if ($GLOBALS['perm']->have_perm("user")) {
            $last_visit = UserConfig::get($GLOBALS['user']->id)->getValue("last_pluginmarket_visit");
            if ($last_visit) {
                $badge_number = MarketPlugin::countBySql("publiclyvisible = 1 AND approved = 1 AND published > ?", array($last_visit));
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

    public function getStudipDomain() {
        if (self::$studip_domain) {
            return self::$studip_domain;
        }
        if (strpos($_SERVER['SERVER_NAME'], ':') !== false) {
            list($_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT']) =
                explode(':', $_SERVER['SERVER_NAME']);
        }
        if ($_SERVER['SERVER_NAME'] === "localhost" || $_SERVER['SERVER_NAME'] = "127.0.0.1") {
            $domain_warning = sprintf(_("Achtung, mit %s als Domain kann der Webhook-Aufruf von github nicht funktionieren."), $_SERVER['SERVER_NAME']);
        }
        $DOMAIN_STUDIP = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
        $DOMAIN_STUDIP .= '://'.$_SERVER['SERVER_NAME'];

        if ($_SERVER['HTTPS'] == 'on' && $_SERVER['SERVER_PORT'] != 443 ||
            $_SERVER['HTTPS'] != 'on' && $_SERVER['SERVER_PORT'] != 80) {
            $DOMAIN_STUDIP .= ':'.$_SERVER['SERVER_PORT'];
        }
        return self::$studip_domain = $DOMAIN_STUDIP;
    }

}