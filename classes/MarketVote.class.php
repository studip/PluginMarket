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
}