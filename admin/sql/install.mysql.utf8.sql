DROP TABLE IF EXISTS `#__mapbox`;
CREATE TABLE `#__mapbox` (
	`mapbox_id` INT(11) unsigned NOT NULL auto_increment,
	`mapbox_name` VARCHAR(127) DEFAULT NULL,
	`mapbox_alias` VARCHAR(32) DEFAULT NULL,
	`mapbox_description` TEXT DEFAULT NULL,
	`attribs` TEXT DEFAULT NULL,
	`meta_description` TEXT DEFAULT NULL,
	`meta_keywords` TEXT DEFAULT NULL,
	`ordering` INT(11) unsigned DEFAULT NULL,
	`published` TINYINT(1) unsigned DEFAULT 0,
	`checked_out` INT(11) unsigned DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT '0000-00-00 00:00:00',
	`modified` DATETIME DEFAULT '0000-00-00 00:00:00',
	`modified_by` INT(11) UNSIGNED DEFAULT 0,
	`access` INT(11) UNSIGNED DEFAULT NULL,
	PRIMARY KEY  (`mapbox_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
