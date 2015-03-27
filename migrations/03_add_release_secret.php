<?php

class AddReleaseSecret extends Migration {
    
    public function up() {
        DBManager::get()->exec("
            ALTER TABLE `pluginmarket_releases`
            ADD `repository_secret` VARCHAR( 32 ) NULL
            AFTER `repository_download_url` ;
        ");
    }
	
}