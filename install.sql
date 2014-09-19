CREATE TABLE IF NOT EXISTS `pluginmarket_plugins` (
    `plugin_id` varchar(32) NOT NULL,
    `name` varchar(255) NOT NULL,
    `description` text,
    `license` varchar(255) NOT NULL,
    `user_id` varchar(32) NOT NULL,
    `in_use` text,
    `short_description` text NOT NULL,
    `release_type` varchar(255) default NULL,
    `approved` tinyint(2) NOT NULL default '0',
    `publiclyvisible` TINYINT NOT NULL DEFAULT '1',
    `donationsaccepted` TINYINT NOT NULL DEFAULT '1',
    `url` varchar(2000) default NULL,
    `classification` enum('firstclass','secondclass','none') NOT NULL default 'none',
    `language` enum('de','en','de_en') NOT NULL default 'de',
    `chdate` int(20) NOT NULL,
    `mkdate` int(20) NOT NULL,
    PRIMARY KEY (`plugin_id`),
    KEY `user_id` (`user_id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `pluginmarket_releases` (
    `release_id` varchar(32) NOT NULL,
    `plugin_id` varchar(32) NOT NULL,
    `version` varchar(255) NOT NULL,
    `studip_min_version` varchar(255) default NULL,
    `studip_max_version` varchar(255) default NULL,
    `user_id` varchar(32) NOT NULL,
    `file_id` varchar(32) default NULL,
    `downloads` int(20) NOT NULL default '0',
    `release_type` varchar(255) default NULL,
    `origin` varchar(255) NULL,
    `repository_download_url` VARCHAR( 128 ) NULL,
    `chdate` int(20) NOT NULL,
    `mkdate` int(20) NOT NULL,
    PRIMARY KEY (`release_id`),
    KEY `plugin_id` (`plugin_id`),
    KEY `user_id` (`user_id`)
) ENGINE=MyISAM;


CREATE TABLE IF NOT EXISTS `pluginmarket_user_plugins` (
    `user_id` varchar(32) NOT NULL,
    `plugin_id` varchar(32) NOT NULL,
    PRIMARY KEY (`user_id`,`plugin_id`)
) ENGINE=MyISAM;

CREATE TABLE IF NOT EXISTS `pluginmarket_tags` (
    `tag` varchar(64) NOT NULL,
    `plugin_id` varchar(32) NOT NULL,
    `proposal` tinyint(4) NOT NULL DEFAULT '0',
    `user_id` varchar(32) NOT NULL,
    PRIMARY KEY (`tag`,`plugin_id`),
    KEY `plugin_id` (`plugin_id`),
    KEY `user_id` (`user_id`)
) ENGINE=MyISAM;

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