<?php
/**
 * Usage:
 * Call with argument 'status' to get the last applied migrations's name.
 *
 * To apply all new migrations:
 * - call the script from terminal without arguments, eg. 'php migrations.php'.
 * To apply only a given number of new migrations:
 * - call the script with 'up' argument only to apply all new migrations, eg. 'php migrations.php up' for all migrations.
 * - call the script with 'up' argument and number of migrations to apply, eg. 'php migrations.php up 2' for two migrations.
 * To remove a given number of migrations:
 * - call the script with 'down' argument and number of migrations to remove, eg. 'php migrations.php down 1' to remove 1 migration.
 */

require_once('config/config.php');
require_once(SYSTEMROOT . 'Database.php');

class Migration {
	protected $db;

	public function __construct(){
		$this->db = Database::dbConnect();
	}

	/**
	 * Apply database migration scripts.
	 *
	 * @param int|bool $count The number of new migrations to run, false to add all new migrations.
	 */
	public function migrationUp($count){
		$this->createTable();
		$previousMigrations = $this->getMigrations();
		$addedMigrations = [];

		// Fetch all migration filenames and remove migrations that have already been completed.
		$files = [];
		foreach(new DirectoryIterator(APPROOT . DS . 'migrations') as $file){
			if($file->isDot()) continue;
			$files[] =  $file->getFilename();
		}
		$newMigrations = array_diff($files, $previousMigrations);

		// Instantiate and execute new migrations.
		$size = sizeof($newMigrations);
		if(!$count)$count = $size;
		else $count = min($count, $size);

		foreach($newMigrations as $migration){
			if($count-- == 0) break;
			require_once(APPROOT . DS . 'migrations' . DS . $migration);
			$className = substr($migration, 0, stripos($migration, '.'));
			echo "Adding migration: " . $className . PHP_EOL;
			$class = new $className();
			if($class->up($this->db)){
				$addedMigrations[] = $migration;
			}
		}

		// Save added migrations to database.
		if(!empty($addedMigrations)){
			$this->addMigrations($addedMigrations);
			echo "Success." . PHP_EOL;
		} else {
			echo "No new migrations found." . PHP_EOL;
		}
	}

	/**
	 * Remove migrations.
	 *
	 * @param int $count The number of last migrations to remove.
	 */
	public function migrationDown($count){
		$previousMigrations = $this->getMigrations();
		$removedMigrations = [];
		$size = sizeof($previousMigrations);
		for($i = $size - 1; $i >= 0; $i--){
			if($count-- == 0) break;
			$migration = $previousMigrations[$i];
			require_once(APPROOT . DS . 'migrations' . DS . $migration);
			$className = substr($migration, 0, stripos($migration, '.'));
			echo "Removing migration: " . $className . PHP_EOL;
			$class = new $className();
			if($class->down($this->db)){
				$removedMigrations[] = $migration;
			}
		}

		// Remove migrations from database.
		if(!empty($removedMigrations)){
			$this->removeMigrations($removedMigrations);
			echo "Success." . PHP_EOL;
		} else {
			echo "No migrations removed." . PHP_EOL;
		}
	}

	/**
	 * Migration status.
	 */
	public function status(){
		echo "Latest migration: " . $this->lastMigration();
	}

	/**
	 * Create migrations table to database if one doesn't exist yet.
	 */
	public function createTable(){
		$this->db->exec("
		CREATE TABLE IF NOT EXISTS `migrations` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			`created` timestamp NOT NULL DEFAULT current_timestamp(),
			PRIMARY KEY (`id`)
		  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
	}

	/**
	 * Read completed migrations from database.
	 *
	 * @return array Returns array of migration file names.
	 */
	public function getMigrations(){
		$statement = $this->db->prepare("SELECT name FROM `migrations`");
		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * Add completed migrations to database.
	*/
	public function addMigrations($migrations){
		$list = implode(',', array_map(fn($migration) => '("' . $migration . '")', $migrations));
		$statement = $this->db->prepare("INSERT INTO migrations (name) VALUES " . $list);
		$statement->execute();
	}

	/**
	 * Remove migrations from database.
	 */
	public function removeMigrations($migrations){
		$list = implode(',', array_map(fn($migration) => '"' . $migration . '"', $migrations));
		$statement = $this->db->prepare("DELETE FROM migrations WHERE name IN (" . $list . ")");
		$statement->execute();
	}

	/**
	 * Get last added migration.
	 *
	 * @return string Name of the last migration in database.
	 */
	public function lastMigration(){
		$statement = $this->db->prepare("SELECT name FROM migrations ORDER BY created DESC LIMIT 1");
		$statement->execute();
		return $statement->fetchColumn();
	}
}

$command = '';
if(isset($argv[1])) $command = strtolower($argv[1]);
$count = false;
if(isset($argv[2])) $count = (int) $argv[2];

$migrations = new Migration();
if($command == '' || $command == 'up'){
	$migrations->migrationUp($count);
}
else if($command == 'down' && $count){
	$migrations->migrationDown($count);
}
else if($command == 'status'){
	$migrations->status();
} else {
	echo 'Argument not recognised.' . PHP_EOL;
}

?>