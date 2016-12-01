<?php

class AddModeratorRole extends Migration {

    public function up() {

        DBManager::get()->exec("
            INSERT IGNORE INTO `roles` (`rolename`, `system`)
            VALUES
                ('Pluginbeauftragter', 'n');
        ");
        StudipCacheFactory::getCache()->expire('plugins/rolepersistence/roles');
    }

    public function down() {
    }

}
