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
        $config['belongs_to']['user'] = array(
            'class_name' => 'User',
            'foreign_key' => 'user_id',
        );
        parent::configure($config);
    }
}