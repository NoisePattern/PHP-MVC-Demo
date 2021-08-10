<?php

class migration_00001 {

	public function up($db){
		try {
			$db->exec("START TRANSACTION");
			$db->exec("
				CREATE TABLE IF NOT EXISTS `users` (
					`user_id` int(11) NOT NULL AUTO_INCREMENT,
					`username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					`email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					`level` int(11) NOT NULL DEFAULT 0,
					`password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					`created` datetime NOT NULL,
					`updated` datetime DEFAULT NULL,
					PRIMARY KEY (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
			$db->exec("
				INSERT INTO `users` (`user_id`, `username`, `email`, `level`, `password`, `created`, `updated`) VALUES(1, 'admin', 'admin@example.com', 2, '" . '$2y$10$dyKOiqRdUgvhLMlnXwnlDeW.AM3MZLjWHxJQbXhCRF2ZZyZ0rkurW' . "', '2021-07-27 20:18:43', NULL);
			");
			$db->exec("
				CREATE TABLE IF NOT EXISTS `articles` (
					`article_id` int(11) NOT NULL AUTO_INCREMENT,
					`user_id` int(11) NOT NULL,
					`caption` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
					`content` text COLLATE utf8_unicode_ci NOT NULL,
					`published` tinyint(1) NOT NULL DEFAULT 0,
					`created` datetime NOT NULL,
					`updated` datetime DEFAULT NULL,
					PRIMARY KEY (`article_id`),
					KEY `user_id` (`user_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
			$db->exec("
				ALTER TABLE `articles`
					ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;
			");
		} catch(Exception $e){
			$db->exec("DROP TABLE IF EXISTS articles;");
			$db->exec("DROP TABLE IF EXISTS users;");
			echo "Error: " . $e->getMessage();
			exit;
		}
		$db->exec("COMMIT");
		echo "Added migration: " . static::class . PHP_EOL;
		return true;
	}

	public function down($db){
		$db->exec("DROP TABLE IF EXISTS articles;");
		$db->exec("DROP TABLE IF EXISTS users;");
		echo "Removed migration: " . static::class . PHP_EOL;
		return true;
	}
}

?>