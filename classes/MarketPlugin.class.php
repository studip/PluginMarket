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