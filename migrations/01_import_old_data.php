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
        /*$releasefiles = $db->query("SELECT file_id FROM pluginmarket_releases")->fetchAll(PDO::FETCH_COLUMN, 0);
        foreach ($releasefiles as $file_id) {
            @copy($oldfilefolder."/".$file_id, $releasefolder."/".$file_id);
        }*/

        //import images
        $db->exec("
            INSERT INTO pluginmarket_images (image_id, plugin_id, position, filename, mimetype, chdate, mkdate)
            SELECT mp_screenshots.file_id, mp_screenshots.plugin_id, IF(mp_screenshots.title_screen > 0, 0, sort + 1), mp_file_content.file_name, 'image/jpg', mp_screenshots.mkdate, mp_screenshots.mkdate
            FROM mp_screenshots
               INNER JOIN mp_file_content ON (mp_file_content.file_id = mp_screenshots.file_id)
        ");
        /*$imagefiles = $db->query("SELECT image_id FROM pluginmarket_images")->fetchAll(PDO::FETCH_COLUMN, 0);
        foreach ($imagefiles as $file_id) {
            @copy($oldfilefolder."/".$file_id, $imagefolder."/".$file_id);
        }*/

        //import tags
        $db->exec("
            INSERT IGNORE INTO pluginmarket_tags (tag, plugin_id, proposal, user_id)
            SELECT mp_tags.tag, mp_tags_objects.object_id, 0, mp_plugins.user_id
            FROM mp_tags
                INNER JOIN mp_tags_objects ON (mp_tags_objects.tag_id = mp_tags.tag_id)
                INNER JOIN mp_plugins ON (mp_tags_objects.object_id = mp_plugins.plugin_id)
        ");

        $db->exec("
            INSERT IGNORE INTO pluginmarket_user_plugins (user_id, plugin_id)
            SELECT user_id, plugin_id FROM mp_user_plugins
        ");

        //move user_ids for the powerusers
        $user_mappings = array(
            '9790cb9e745cb7d85d3bc5a00141c851' => "2be5757744a32e6fe9a591f628f85ae8", //Jan Kulmann
            '8fcc07aaadbc82bb8e72a4faa1ae42d0' => "f28e9576efd238287b8db87ac1119087", //André Noack
            '2ee0aa5f6dcd48b945a1c786f488c148' => "f953070076eadbfd898e9552a35f5d95", //Jan-Hendrik Wilms
            '17bf68a718c9ebc0010b96fb5fb99bc6' => "a704f6b9e0881ea966ae70a45483fe91", //Florian Bieringer
            '930f2b06cf0b2e088c295a2c7775d3ea' => "1616d8e6e7f1021da600ce016674e3c5", //Till Glöggler
            'e7d1aa70c09882f57906e598319afcd8' => "a32ec5d4b3da276e71653a7d66511dc7", //Elmar Ludwig
            '98a5b6860577340912c0522c4717cf25' => "74b76020d6572a22aa66c3644af74619", //Oliver Oster
            '1f27f5c4d0be374f74bac398b9a025e0' => "0e373b8b062fb2cd17502181a47a64d6", //Bernhard Strehl
            '36e8c9f4b4c8a07393d3687a7dcb7610' => "fdc5720c87e94d6d4594f2daa6bfd384", //Tobias Thelen
            '75c643d817cfef8741ba26259315db77' => "58abc52d98e2fb51f4409481cdab39f3", //Rasmus Fuhse
            'f41cb64714f820d10d094958ed9d6d9d' => "d3bb8252247c6decf759170b7a0cbb49", //Johannes Stichler
            '53d48eed2e6f1390b6e97f38f70d424a' => "23fbf006cb81fa8db4d2404a59758c6c", //Nico Müller
            '9afe50de3e49fceef0fee915f077f958' => "4e6e5879260347913ee16e6e7d39de4e", //Thomas Hackl
            'e52aeb847121e1878af017a933480960' => "38e5d370b109239c6e25137c19232e1d", //Eric Laubmeyer
            '384bd46f74a845fe5e70f23e909d0b8e' => "ca5b8e769f71a7b66f030cbcf346ea62", //Robert Costa
            '17ab55f49e50497992ae44cb306231e0' => "20fabec10a53dfab3615bf5ee491dd5a", //Marcus Lunzenauer
            '37f6c72b598d64bf082a7fc0c8fa6811' => "0e5487512b84adfe83fc3fef055658f0", //Stefan Suchi
            'b64fc94a38ead9119c936a8056c9cceb' => "790a3ccb7bcebd5fa9dcbbdbd74429b2", //Jörg Röpke
            '8f803630b5fa7756d1b2431436c4baff' => "3f1503ee51b2a326fec61efa35839fc2", //Arne Schröder
            '49d15355f3fbe8b15c59f949d362747d' => "4abd1b0ca7fd763016316bec212e3866", //Stefan Osterloh
            'c28b58bbe8e0755b5b6e2784160aa35b' => "84476bf266debf59c34f76ac82f59a03", //Lennart G
            '64cba677908f2a058d20c67a5ada7663' => "ba850607ce72dd2d2cbe46361c91ecab", //Olga Mertsalova
        );
        $change_plugin_user = $db->prepare("
            UPDATE pluginmarket_plugins SET user_id = :new WHERE user_id = :old
        ");
        $change_plugin_user_connection = $db->prepare("
            UPDATE pluginmarket_user_plugins SET user_id = :new WHERE user_id = :old
        ");
        $change_releases = $db->prepare("
            UPDATE pluginmarket_releases SET user_id = :new WHERE user_id = :old
        ");
        $change_plugin_tags = $db->prepare("
            UPDATE pluginmarket_tags SET user_id = :new WHERE user_id = :old
        ");
        foreach ($user_mappings as $old_user_id => $user_id) {
            $change_plugin_user->execute(array(
                'old' => $old_user_id,
                'new' => $user_id
            ));
            $change_plugin_user_connection->execute(array(
                'old' => $old_user_id,
                'new' => $user_id
            ));
            $change_releases->execute(array(
                'old' => $old_user_id,
                'new' => $user_id
            ));
            $change_plugin_tags->execute(array(
                'old' => $old_user_id,
                'new' => $user_id
            ));
        }

    }
	
    public function down() {

    }
}