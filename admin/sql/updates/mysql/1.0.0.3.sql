DROP TABLE IF EXISTS `#__mapbox_templates`;
CREATE TABLE `#__mapbox_templates` (
    `template_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `template_name` VARCHAR(64) DEFAULT NULL,
    `template_layout` VARCHAR(64) DEFAULT NULL,
    PRIMARY KEY (`template_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

ALTER TABLE `#__mapbox_maps` ENGINE = InnoDB;
ALTER TABLE `#__mapbox_markers` ENGINE = InnoDB;
