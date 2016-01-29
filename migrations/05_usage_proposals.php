<?php

class UsageProposals extends Migration {

    public function up() {

        $db = DBManager::get();

        // Setup new table
        $db->exec("
            CREATE TABLE IF NOT EXISTS `pluginmarket_plugin_usages` (
  `usage_id` varchar(32) NOT NULL,
  `plugin_id` varchar(32) NOT NULL,
  `user_id` varchar(32) NOT NULL,
  `name` varchar(128) NOT NULL,
  `approved` tinyint(4) NOT NULL DEFAULT 0,
  `chdate` int(11) NOT NULL,
  `mkdate` int(11) NOT NULL,
  PRIMARY KEY (`usage_id`),
  KEY (`plugin_id`),
  KEY (`user_id`),
  KEY (`name`)
) ENGINE=MyISAM
        ");
        
        SimpleORMap::expireTableScheme();

        $stmt = $db->query("SELECT * FROM pluginmarket_plugins");
        while ($plugin = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hits = preg_split("/(?:\n|,|\t)/", $plugin['in_use']);
            $hits = array_map('trim', $hits);
            $hits = array_filter($hits);
            foreach ($hits as $hit) {
                MarketPluginUsage::create(array(
                    'plugin_id' => $plugin['plugin_id'],
                    'user_id' => $plugin['user_id'],
                    'name' => $hit
                ));
            }
        }


        // Modify old table
        $db->exec("ALTER TABLE pluginmarket_plugins DROP COLUMN in_use");
    }

    public function down() {
        DBManager::get()->exec("DROP TABLE IF EXISTS `pluginmarket_plugin_usages`");
    }

}
