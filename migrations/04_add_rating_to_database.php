<?php

class AddRatingToDatabase extends Migration {
    
    public function up() {
        DBManager::get()->exec("
            ALTER TABLE `pluginmarket_plugins`
            ADD `rating` DOUBLE NULL AFTER `language` ;
        ");
        foreach (MarketPlugin::findBySQL("1=1") as $plugin) {
            $plugin['rating'] = $plugin->calculateRating();
            $plugin->store();
        }
    }
	
}