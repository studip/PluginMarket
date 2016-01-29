<?php

class MarketReview extends SimpleORMap {

    protected static function configure($config = array())
    {
        $config['db_table'] = 'pluginmarket_reviews';
        $config['belongs_to']['plugin'] = array(
            'class_name' => 'MarketPlugin',
            'foreign_key' => 'plugin_id',
        );
        parent::configure($config);
    }

    public static function findByPlugin_id ($plugin_id) {
        return self::findBySQL("plugin_id = ? ORDER BY mkdate DESC", array($plugin_id));
    }
}