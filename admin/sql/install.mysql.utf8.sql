DROP TABLE IF EXISTS `#__mapbox_maps`;
CREATE TABLE `#__mapbox_maps` (
	`map_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`map_alias` VARCHAR(96) DEFAULT NULL,
	`map_api_key` VARCHAR(32) DEFAULT NULL,
	`map_name` VARCHAR(96) DEFAULT NULL,
	`map_description` TEXT DEFAULT NULL,
	`attribs` TEXT DEFAULT NULL,
	`ordering` INT(11) UNSIGNED DEFAULT NULL,
	`published` tinyINT(1) UNSIGNED DEFAULT 0,
	`checked_out` INT(11) UNSIGNED DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT '0000-00-00 00:00:00',
	`access` TINYINT(3) DEFAULT 0,
	`modified_by` INT(11) UNSIGNED DEFAULT 0,
	`created_by` INT(11) UNSIGNED DEFAULT 0,
	PRIMARY KEY (`map_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__mapbox_markers`;
CREATE TABLE `#__mapbox_markers` (
	`marker_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`marker_name` VARCHAR(64) DEFAULT NULL,
	`marker_lat` FLOAT DEFAULT 0,
	`marker_lng` FLOAT DEFAULT 0,
	`marker_description` TEXT DEFAULT NULL,
	`attribs` TEXT DEFAULT NULL,
	`ordering` INT(11) UNSIGNED DEFAULT NULL,
	`published` tinyINT(1) UNSIGNED DEFAULT 0,
	`checked_out` INT(11) UNSIGNED DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT '0000-00-00 00:00:00',
	`access` TINYINT(3) DEFAULT 0,
	`modified_by` INT(11) UNSIGNED DEFAULT 0,
	`created_by` INT(11) UNSIGNED DEFAULT 0,
	`map_id` INT(11) UNSIGNED DEFAULT NULL,
	PRIMARY KEY (`marker_id`),
	KEY `map_id` (`map_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
