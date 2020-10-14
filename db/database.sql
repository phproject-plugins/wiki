SET NAMES utf8mb4;

DROP TABLE IF EXISTS `wiki_page`;
CREATE TABLE `wiki_page` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`slug` varchar(64) NOT NULL,
	`content` mediumtext NOT NULL,
	`parent_id` int(10) unsigned DEFAULT NULL,
	`created_date` datetime NOT NULL,
	`deleted_date` datetime NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `wiki_page_update`;
CREATE TABLE `wiki_page_update` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`wiki_page_id` int(10) unsigned NOT NULL,
	`user_id` int(10) unsigned NOT NULL,
	`old_name` varchar(64) DEFAULT NULL,
	`new_name` varchar(64) NOT NULL,
	`old_content` mediumtext,
	`new_content` mediumtext NOT NULL,
	`created_date` datetime NOT NULL,
	PRIMARY KEY (`id`),
	KEY `wiki_page_update_user` (`user_id`),
	CONSTRAINT `wiki_page_update_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
