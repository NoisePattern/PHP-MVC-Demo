<?php

class migration_00003 {

	public function up($db){
		$db->exec("START TRANSACTION");
		try {
			$db->exec("
				CREATE TABLE IF NOT EXISTS `rbac_roles` (
					`role_id` int(11) NOT NULL AUTO_INCREMENT,
					`role_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
					PRIMARY KEY (`role_id`)
				) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
			$db->exec("
				INSERT INTO `rbac_roles` (`role_id`, `role_name`) VALUES
					(1, 'User'),
					(2, 'Administrator');
			");
			$db->exec("
			CREATE TABLE IF NOT EXISTS `rbac_permissions` (
				`permission_id` int(11) NOT NULL AUTO_INCREMENT,
				`permission_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
				`permission_description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
				PRIMARY KEY (`permission_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
			$db->exec("
				INSERT INTO `rbac_permissions` (`permission_id`, `permission_name`, `permission_description`) VALUES
					(1, 'manageAllArticles', 'Manage all articles'),
					(2, 'manageOwnArticles', 'Manage own articles'),
					(3, 'writeArticles', 'Write articles'),
					(4, 'updateAllArticles', 'Update all articles.'),
					(5, 'updateOwnArticles', 'Update own articles.'),
					(6, 'deleteAllArticles', 'Delete any article.'),
					(7, 'deleteOwnArticles', 'Delete own articles.'),
					(8, 'manageGalleries', 'Manage galleries'),
					(9, 'addGalleries', 'Add galleries'),
					(10, 'deleteGalleries', 'Delete galleries.'),
					(11, 'manageAllImages', 'Manage all images.'),
					(12, 'manageOwnImages', 'manage own images.'),
					(13, 'addImages', 'Upload images.'),
					(14, 'deleteAllImages', 'Delete all images.'),
					(15, 'deleteOwnImages', 'Delete own images.');
			");
			$db->exec("
				CREATE TABLE IF NOT EXISTS `rbac_roles_permissions` (
					`rp_id` int(11) NOT NULL AUTO_INCREMENT,
					`role_id` int(11) NOT NULL,
					`permission_id` int(11) NOT NULL,
					PRIMARY KEY (`rp_id`),
					KEY `role_id` (`role_id`),
					KEY `permission_id` (`permission_id`)
				) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
			$db->exec("
				INSERT INTO `rbac_roles_permissions` (`rp_id`, `role_id`, `permission_id`) VALUES
					(1, 1, 2),
					(2, 1, 3),
					(3, 1, 5),
					(4, 1, 7),
					(5, 1, 12),
					(6, 1, 13),
					(7, 1, 15),
					(8, 2, 1),
					(9, 2, 2),
					(10, 2, 3),
					(11, 2, 4),
					(12, 2, 5),
					(13, 2, 6),
					(14, 2, 7),
					(15, 2, 8),
					(16, 2, 9),
					(17, 2, 10),
					(18, 2, 11),
					(19, 2, 12),
					(20, 2, 13),
					(21, 2, 14),
					(22, 2, 15);
			");
			$db->exec("
				ALTER TABLE `rbac_roles_permissions`
					ADD CONSTRAINT `rbac_roles_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `rbac_roles` (`role_id`) ON UPDATE CASCADE,
					ADD CONSTRAINT `rbac_roles_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `rbac_permissions` (`permission_id`) ON UPDATE CASCADE;
			");
			$db->exec("
				CREATE TABLE IF NOT EXISTS `rbac_rules` (
					`rule_id` int(11) NOT NULL AUTO_INCREMENT,
					`rule_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
					PRIMARY KEY (`rule_id`)
				) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
			$db->exec("
				INSERT INTO `rbac_rules` (`rule_id`, `rule_name`) VALUES
					(1, 'isOwner');
			");
			$db->exec("
			CREATE TABLE IF NOT EXISTS `rbac_permissions_rules` (
				`pr_id` int(11) NOT NULL AUTO_INCREMENT,
				`permission_id` int(11) NOT NULL,
				`rule_id` int(11) NOT NULL,
				PRIMARY KEY (`pr_id`),
				KEY `rbac_permissions_rules_ibfk_1` (`permission_id`),
				KEY `rbac_permissions_rules_ibfk_2` (`rule_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
			$db->exec("
				INSERT INTO `rbac_permissions_rules` (`pr_id`, `permission_id`, `rule_id`) VALUES
					(1, 5, 1),
					(2, 7, 1),
					(3, 15, 1);
			");
			$db->exec("
				ALTER TABLE `rbac_permissions_rules`
					ADD CONSTRAINT `rbac_permissions_rules_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `rbac_permissions` (`permission_id`) ON UPDATE CASCADE,
					ADD CONSTRAINT `rbac_permissions_rules_ibfk_2` FOREIGN KEY (`rule_id`) REFERENCES `rbac_rules` (`rule_id`) ON UPDATE CASCADE;
			");
			$db->exec("
				ALTER TABLE `users` CHANGE `level` `role_id` INT(11) NOT NULL DEFAULT '0';
			");
			$db->exec("
				ALTER TABLE `users` ADD CONSTRAINT `user_role` FOREIGN KEY (`role_id`) REFERENCES `rbac_roles`(`role_id`) ON DELETE RESTRICT ON UPDATE CASCADE;
			");
		}
		catch (Exception $e){
			$db->exec("DROP TABLE IF EXISTS rbac_permissions_rules");
			$db->exec("DROP TABLE IF EXISTS rbac_rules");
			$db->exec("DROP TABLE IF EXISTS rbac_roles_permissions");
			$db->exec("DROP TABLE IF EXISTS rbac_permissions");
			$db->exec("DROP TABLE IF EXISTS rbac_roles");
			$db->exec("ROLLBACK");
			echo "Error: " . $e->getMessage();
			exit;
		}
		$db->exec("COMMIT");
		echo "Added migration: " . static::class . PHP_EOL;
		return true;
	}

	public function down($db){
		$db->exec("START TRANSACTION");
		try {
			$db->exec("
				ALTER TABLE `users` CHANGE `role_id` `level` INT(11) NOT NULL DEFAULT '0';
			");
			$db->exec("
				ALTER TABLE `users` DROP FOREIGN KEY `user_role`;
			");
			$db->exec("
				ALTER TABLE `users` DROP INDEX `user_role`;
			");
			$db->exec("
				DROP TABLE IF EXISTS rbac_permissions_rules;
			");
			$db->exec("
				DROP TABLE IF EXISTS rbac_rules;
			");
			$db->exec("
				DROP TABLE IF EXISTS rbac_roles_permissions;
			");
			$db->exec("
				DROP TABLE IF EXISTS rbac_permissions;
			");
			$db->exec("
				DROP TABLE IF EXISTS rbac_roles;
			");
		} catch (Exception $e){
			$db->exec("ROLLBACK");
			echo "Error: " . $e->getMessage();
			exit;
		}
		$db->exec("COMMIT");
		echo "Removed migration: " . static::class . PHP_EOL;
		return true;
	}
}

?>