<?php

class MarketReleaseFollower extends SimpleORMap {

    static public function findByUserAndRelease($user_id, $release_id) {
        return self::findOneBySQL("user_id = ? AND release_id = ?", array($user_id, $release_id));
    }

    protected static function configure($config = array())
    {
        $config['db_table'] = 'pluginmarket_release_followers';
        $config['belongs_to']['release'] = array(
            'class_name' => 'MarketRelease',
            'foreign_key' => 'release_id',
        );
        parent::configure($config);
    }
}