<?php
class DeprecatedPlugins extends Migration {

    public function up() {

        DBManager::get()->exec("
            ALTER TABLE `pluginmarket_plugins` ADD COLUMN `deprecated` TINYINT(1) NOT NULL DEFAULT '0' AFTER `rating`
        ");
        SimpleORMap::expireTableScheme();
    }

    public function down() {
    }

}