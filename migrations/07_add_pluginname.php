<?php
class AddPluginname extends Migration {

    public function up() {

        DBManager::get()->exec("
            ALTER TABLE `pluginmarket_plugins` CHANGE `pluginclassname` `pluginname` VARCHAR(64) NOT NULL
        ");
        SimpleORMap::expireTableScheme();
        foreach (MarketPlugin::findBySQL("1") as $plugin) {
            if ($plugin->releases->count()) {
                $pluginnames = array_count_values(array_filter($plugin->releases->getPluginName()));
                arsort($pluginnames);
                $pluginname = key($pluginnames);
                if ($pluginname) {
                    $plugin->pluginname = $pluginname;

                    $plugin->releases->each(function ($one) use ($pluginname) {
                        if ($one->getPluginName() != $pluginname) {
                            $one->delete();
                        }
                    });
                } else {
                    $plugin->name .= " (no pluginname)";
                    $plugin->releases->delete();
                    $plugin->approved = 0;
                }
            } else {
                $plugin->name .= " (no release)";
                $plugin->approved = 0;
            }
            $plugin->store();
        }
    }

    public function down() {
    }

}