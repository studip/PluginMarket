<?php

class ImportOldData extends Migration {
    
    function description() {
        return 'creates the database and imports all the old data';
    }

    public function up() {
        $db = DBManager::get();
        $oldfilefolder = $GLOBALS['STUDIP_BASE_PATH']."/data/old_marketplace_files";

        $releasefolder = $GLOBALS['STUDIP_BASE_PATH'] . "/data/pluginmarket_releases";
        $imagefolder = $GLOBALS['STUDIP_BASE_PATH'] . "/data/pluginmarket_images";

        $db->exec("
            CREATE TABLE IF NOT EXISTS `pluginmarket_plugins` (
                `plugin_id` varchar(32) NOT NULL,
                `name` varchar(255) NOT NULL,
                `description` text,
                `user_id` varchar(32) NOT NULL,
                `in_use` text,
                `short_description` text NOT NULL,
                `approved` tinyint(2) NOT NULL default '0',
                `published` BIGINT NULL,
                `publiclyvisible` TINYINT NOT NULL DEFAULT '1',
                `donationsaccepted` TINYINT NOT NULL DEFAULT '1',
                `url` varchar(2000) default NULL,
                `language` enum('de','en','de_en') NOT NULL default 'de',
                `chdate` int(20) NOT NULL,
                `mkdate` int(20) NOT NULL,
                PRIMARY KEY (`plugin_id`),
                KEY `user_id` (`user_id`),
                FULLTEXT KEY `searchdescription` (`description`,`short_description`)
            ) ENGINE=MyISAM;
        ");
        $db->exec("
            CREATE TABLE IF NOT EXISTS `pluginmarket_releases` (
                `release_id` varchar(32) NOT NULL,
                `plugin_id` varchar(32) NOT NULL,
                `version` varchar(255) NOT NULL,
                `studip_min_version` varchar(255) default NULL,
                `studip_max_version` varchar(255) default NULL,
                `user_id` varchar(32) NOT NULL,
                `file_id` varchar(32) default NULL,
                `downloads` int(20) NOT NULL default '0',
                `origin` varchar(255) NULL,
                `repository_download_url` VARCHAR( 128 ) NULL,
                `chdate` int(20) NOT NULL,
                `mkdate` int(20) NOT NULL,
                PRIMARY KEY (`release_id`),
                KEY `plugin_id` (`plugin_id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=MyISAM;
        ");
        $db->exec("
            CREATE TABLE IF NOT EXISTS `pluginmarket_user_plugins` (
                `user_id` varchar(32) NOT NULL,
                `plugin_id` varchar(32) NOT NULL,
                PRIMARY KEY (`user_id`,`plugin_id`)
            ) ENGINE=MyISAM;
        ");
        $db->exec("
            CREATE TABLE IF NOT EXISTS `pluginmarket_tags` (
                `tag` varchar(64) NOT NULL,
                `plugin_id` varchar(32) NOT NULL,
                `proposal` tinyint(4) NOT NULL DEFAULT '0',
                `user_id` varchar(32) NOT NULL,
                PRIMARY KEY (`tag`,`plugin_id`),
                KEY `plugin_id` (`plugin_id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=MyISAM;
        ");
        $db->exec("
            CREATE TABLE IF NOT EXISTS `pluginmarket_reviews` (
                `review_id` varchar(32) NOT NULL,
                `plugin_id` varchar(32) NOT NULL,
                `user_id` varchar(32) NOT NULL,
                `rating` int(11) NOT NULL,
                `review` TEXT NULL,
                `chdate` int(11) NOT NULL,
                `mkdate` int(11) NOT NULL,
                PRIMARY KEY (`review_id`),
                UNIQUE KEY `unique_votes` (`plugin_id`,`user_id`),
                KEY `plugin_id` (`plugin_id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=MyISAM;
        ");
        $db->exec("
            CREATE TABLE IF NOT EXISTS `pluginmarket_images` (
                `image_id` varchar(32) NOT NULL,
                `plugin_id` varchar(32) NOT NULL,
                `filename` varchar(128) NOT NULL,
                `mimetype` varchar(64) NOT NULL,
                `position` int(20) NOT NULL default '0',
                `mkdate` int(20) NOT NULL,
                `chdate` int(20) NOT NULL,
                PRIMARY KEY (`image_id`),
                KEY `plugin_id` (`plugin_id`)
            ) ENGINE=MyISAM
        ");


        //import plugins
        $db->exec("
            INSERT INTO pluginmarket_plugins (plugin_id, name, description, user_id, in_use, short_description, approved, published, publiclyvisible, donationsaccepted, url, language, chdate, mkdate)
            SELECT plugin_id, name, description, user_id, in_use, short_description, approved, approved, 1, 0, url, language, mkdate, mkdate FROM mp_plugins
        ");
        $db->exec("
            INSERT INTO pluginmarket_releases (release_id, plugin_id, version, studip_min_version, studip_max_version, user_id, file_id, downloads, origin, chdate, mkdate)
            SELECT release_id, plugin_id, version, studip_min_version, studip_max_version, user_id, mp_releases.file_id, downloads, origin, mkdate, mkdate
            FROM mp_releases
        ");
        $releasefiles = $db->query("SELECT file_id FROM pluginmarket_releases")->fetchAll(PDO::FETCH_COLUMN, 0);
        foreach ($releasefiles as $file_id) {
            @copy($oldfilefolder."/".$file_id, $releasefolder."/".$file_id);
        }

        //import images
        $db->exec("
            INSERT INTO pluginmarket_images (image_id, plugin_id, position, filename, mimetype, chdate, mkdate)
            SELECT mp_screenshots.file_id, mp_screenshots.plugin_id, IF(mp_screenshots.title_screen > 0, 0, sort + 1), mp_file_content.file_name, 'image/jpg', mp_screenshots.mkdate, mp_screenshots.mkdate
            FROM mp_screenshots
               INNER JOIN mp_file_content ON (mp_file_content.file_id = mp_screenshots.file_id)
        ");
        $imagefiles = $db->query("SELECT image_id FROM pluginmarket_images")->fetchAll(PDO::FETCH_COLUMN, 0);
        foreach ($imagefiles as $file_id) {
            @copy($oldfilefolder."/".$file_id, $imagefolder."/".$file_id);
        }

        //import tags
        $db->exec("
            INSERT IGNORE INTO pluginmarket_tags (tag, plugin_id, proposal, user_id)
            SELECT mp_tags.tag, mp_tags_objects.object_id, 0, mp_plugins.user_id
            FROM mp_tags
                INNER JOIN mp_tags_objects ON (mp_tags_objects.tag_id = mp_tags.tag_id)
                INNER JOIN mp_plugins ON (mp_tags_objects.object_id = mp_plugins.plugin_id)
        ");

    }
	
    public function down() {

    }
}