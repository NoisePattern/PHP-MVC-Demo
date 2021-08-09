<?php

/**
 * Database connection. Class is abstract so there's no accidental instantiation.
 */
abstract class Database {
	private static $db = null;

	/**
	 * Returns database connection.
	 *
	 * @return object Returns DB connection details.
	 */
	public static function dbConnect(){
		// If there is no conenction yet, establish connection.
		if(is_null(self::$db)){
			$query = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
			$options = [
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			];
			try {
				self::$db = new PDO($query, DB_USERNAME, DB_PASSWORD, $options);
			} catch(PDOException $e){
				echo $e->getMessage();
			}
		}
		return self::$db;
	}
}
?>