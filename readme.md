Product Manager
====================================

CREATE TABLE `products` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`image` VARCHAR(500) NULL DEFAULT NULL,
	`name` VARCHAR(200) NOT NULL DEFAULT '0',
	`sku` VARCHAR(50) NOT NULL,
	`update_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1800
;





CREATE TABLE `product_attibute_update_log` (
	`record_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`worker_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`updated_attribute` VARCHAR(50) NOT NULL DEFAULT '0',
	`value` VARCHAR(1024) NOT NULL DEFAULT '0',
	`updated_sku` VARCHAR(50) NOT NULL DEFAULT '0',
	`update_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`record_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=39354
;

CREATE TABLE `workers` (
	`acc_id` INT(11) NOT NULL AUTO_INCREMENT,
	`acc_name` VARCHAR(128) NOT NULL,
	`acc_login` VARCHAR(50) NOT NULL,
	`acc_password` VARCHAR(256) NOT NULL,
	`is_admin` INT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`acc_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=35
;

Standalone Product Manager
========================================
CREATE TABLE `standalone_product_eav_attributes` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`attribute_name` VARCHAR(500) NOT NULL,
	`visible_on_form` INT(1) NULL DEFAULT '1',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=119
;


CREATE TABLE `standalone_product_eav_attribute_values` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`sku` VARCHAR(500) NOT NULL,
	`attribute_id` VARCHAR(500) NOT NULL,
	`value` VARCHAR(2000) NULL DEFAULT NULL,
	`worker_id` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=31770
;


Social Media Post manager:
==========================================
CREATE TABLE `justin_workers` (
	`acc_id` INT(11) NOT NULL AUTO_INCREMENT,
	`acc_name` VARCHAR(128) NOT NULL,
	`acc_login` VARCHAR(50) NOT NULL,
	`acc_password` VARCHAR(256) NOT NULL,
	`is_admin` INT(1) NULL DEFAULT NULL,
	PRIMARY KEY (`acc_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=39
;



CREATE TABLE `justin_posts` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`worker_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
	`social_media` VARCHAR(2000) NOT NULL DEFAULT '0',
	`text` VARCHAR(5000) NOT NULL,
	`link` VARCHAR(2000) NOT NULL DEFAULT '0',
	`image` VARCHAR(500) NULL DEFAULT NULL,
	`update_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`tags` VARCHAR(1000) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=479
;


