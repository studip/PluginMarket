<?php

class MarketPlugin extends SimpleORMap {
    protected static function configure($config = array())
    {
        $config['db_table'] = 'pluginmarket_plugins';
        $config['has_many']['releases'] = array(
            'class_name' => 'MarketRelease',
            'on_delete' => 'delete',
            'on_store' => 'store',
        );
        $config['has_many']['images'] = array(
            'class_name' => 'MarketImage',
            'on_delete' => 'delete',
            'on_store' => 'store',
        );
        $config['belongs_to']['user'] = array(
            'class_name' => 'User',
            'foreign_key' => 'user_id',
        );
        parent::configure($config);
    }

    public function __construct($id = null)
    {
        $this->registerCallback('before_store', 'requestReview');
        parent::__construct($id);
    }

    public function requestReview() {
        if ($this->content['publiclyvisible'] && !$this->content_db['publiclyvisible'] && !$this['approved']) {
            $messaging = new messaging();
            foreach (User::findByPerms("root") as $rootuser) {
                $messaging->sendSystemMessage(
                    $rootuser['user_id'],
                    _("Plugin %s braucht ein Review"),
                    _("Auf dem Marktplatz wurde ein neues Plugin öffentlich geschaltet. Es kann allerdings erst öffentlich auf dem Marktplatz erscheinen, wenn Sie das Plugin einmal reviewt haben und freischalten. Gehen Sie auf den Pluginmarktplatz und den Reiter 'Qualitätssicherung'.")
                );
            }
        }
    }

    public function isWritable($user_id = null) {
        $user_id || $user_id = $GLOBALS['user']->id;
        return $this['user_id'] === $user_id;
    }

    public function isRootable($user_id = null) {
        $user_id || $user_id = $GLOBALS['user']->id;
        return $GLOBALS['perm']->have_perm("root", $user_id);
    }

    public function getLogoURL() {
        $firstimage = $this->images->first();
        return $firstimage ? $firstimage->getURL() : null;
    }
}