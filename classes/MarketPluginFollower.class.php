<?php

class MarketPluginFollower extends SimpleORMap {

    static public function findByUserAndPlugin($user_id, $plugin_id) {
        return self::findOneBySQL("user_id = ? AND plugin_id = ?", array($user_id, $plugin_id));
    }

    protected static function configure($config = array())
    {
        $config['db_table'] = 'pluginmarket_plugin_follower';
        $config['belongs_to']['plugin'] = array(
            'class_name' => 'MarketPlugin',
            'foreign_key' => 'plugin_id',
        );
        parent::configure($config);
    }
}