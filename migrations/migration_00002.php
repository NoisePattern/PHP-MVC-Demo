<?php

class migration_00002 {

	public function up($db){
		try {
			$db->exec("START TRANSACTION");
			$db->exec("
			CREATE TABLE IF NOT EXISTS `galleries` (
				`gallery_id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`filepath` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
				`parent` int(11) DEFAULT NULL,
				`public` tinyint(1) NOT NULL DEFAULT 0,
				PRIMARY KEY (`gallery_id`)
			 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
			$db->exec("
			CREATE TABLE IF NOT EXISTS `galleryimages` (
				`image_id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`gallery_id` int(11) NOT NULL,
				`user_id` int(11) NOT NULL,
				`created` datetime NOT NULL,
				PRIMARY KEY (`image_id`),
				KEY `gallery_id` (`gallery_id`),
				KEY `user_id` (`user_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
			$db->exec("
			ALTER TABLE `galleryimages`
				ADD CONSTRAINT `galleryimages_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `galleries` (`gallery_id`) ON UPDATE CASCADE,
				ADD CONSTRAINT `galleryimages_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;
			");
		} catch(Exception $e){
			$db->exec("ALTER TABLE galleryimages DROP FOREIGN KEY galleryimages_ibfk_1;");
			$db->exec("DROP TABLE IF EXISTS galleries;");
			$db->exec("DROP TABLE IF EXISTS galleryimages;");
			echo "Error: " . $e->getMessage();
			exit;
		}
		$db->exec("COMMIT");
		echo "Added migration: " . static::class . PHP_EOL;
		return true;
	}

	public function down($db){
		$db->exec("ALTER TABLE galleryimages DROP FOREIGN KEY galleryimages_ibfk_1;");
		$db->exec("DROP TABLE IF EXISTS galleries;");
		$db->exec("DROP TABLE IF EXISTS galleryimages;");
		echo "Removed migration: " . static::class . PHP_EOL;
		return true;
	}
}

?>