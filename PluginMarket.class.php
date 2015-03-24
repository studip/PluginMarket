<?php
require_once 'bootstrap.php';

class PluginMarket extends StudIPPlugin implements SystemPlugin, HomepagePlugin
{
    static protected $studip_domain = null;

    public function __construct()
    {
        parent::__construct();
        $top = new Navigation($this->getDisplayTitle(), PluginEngine::getURL($this, array(), "presenting/overview"));
        $top->setImage($this->getPluginURL()."/assets/topicon.svg");

        $overview = new Navigation($this->getDisplayTitle(), PluginEngine::getURL($this, array(), "presenting/overview"));
        $top->addSubNavigation("presenting", $overview);
        $overview->addSubNavigation("overview", new AutoNavigation(_('Übersicht'), PluginEngine::getURL($this, array(), "presenting/overview")));
        $overview->addSubNavigation("all", new AutoNavigation(_('Alle Plugins'), PluginEngine::getURL($this, array(), "presenting/all")));

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

        NotificationCenter::addObserver($this, "triggerFollowingStudips", "PluginReleaseDidUpdateCode");
    }

    public function initialize()
    {
        $this->addStylesheet('assets/pluginmarket.less');
        PageLayout::addHeadElement('link', array(
            'rel'   => 'alternate',
            'type'  => 'application/rss+xml',
            'href'  => PluginEngine::getLink($this, array(), 'rss/newest'),
            'title' => _('Neueste Plugins'),
        ));

        $sidebar = Sidebar::Get();
        $sidebar->setImage('../../'.$this->getPluginPath().'/assets/sidebar-marketplace.png');
    }

    public function getDisplayTitle()
    {
        return _("PluginMarktplatz");
    }

    public function getHomepageTemplate($user_id)
    {
        $this->addStylesheet('assets/pluginmarket.less');

        $templatefactory = new Flexi_TemplateFactory(__DIR__."/views");
        $template = $templatefactory->open("presenting/users_plugins.php");
        $plugins = MarketPlugin::findBySQL("user_id = ? AND publiclyvisible = 1 AND approved = 1 ORDER BY mkdate DESC", array($user_id));
        $template->set_attribute("plugin", $this);
        $template->set_attribute("plugins", $plugins);
        $template->set_attribute("title", _("Meine Plugins"));
        return count($plugins) ? $template : null;
    }

    static public function triggerFollowingStudips($eventname, $release)
    {
        $output = array();
        $payload = json_encode(studip_utf8encode($output));

        foreach ($release->followers as $follower) {
            $header = array();

            if ($follower['security_token']) {
                $calculatedHash = hash_hmac("sha1", $payload, $follower['security_token']);
                $header[] = "X_HUB_SIGNATURE: ".$calculatedHash;
            }
            $header[] = "Content-Type: application/json";

            $r = curl_init();
            curl_setopt($r, CURLOPT_URL, $follower['url']);
            curl_setopt($r, CURLOPT_POST, true);
            curl_setopt($r, CURLOPT_HTTPHEADER, $header);

            curl_setopt($r, CURLOPT_POSTFIELDS, $payload);

            $result = curl_exec($r);
            curl_close($r);
        }
    }

}