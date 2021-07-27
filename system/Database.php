<?php

// Database connection. Class is abstract so there's no accidental instantiation.
abstract class Database {
	private static $db = null;

	public static function dbConnect(){
		if(is_null(self::$db)){
			$query = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
			$options = [
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			];
			try {
				self::$db = new PDO($query, DB_USERNAME, DB_PASSWORD, $options);
			} catch(PDOException $e){
				setErrorPage('Database connection error', 'Request to database failed with error: ' . $e->getMessage());
				header('location:' . URLROOT . DS . 'defaults' . DS . 'error');
			}
		}
		return self::$db;
	}
}
?>