<?php

class Gallery extends Model {

	/**
	 * Name of the associated table in DB.
	 *
	 * @return string Returns table name.
	 */
	public function tableName(){
		return 'galleries';
	}

	/**
	 * Model variables, one for each DB table field, plus any other fields the form returns (but will not save to DB).
	 */
	public $gallery_id;
	public $name;
	public $filepath;
	public $parent_id;
	public $public;
	public $selectedGallery;

	/**
	 * Name of DB table's primary key.
	 *
	 * @return string Name of primary key.
	 */
	public function getPrimaryKey(){
		return 'gallery_id';
	}

	/**
	 * Names of fields in DB that can be set by model when an entry is created or updated. Each name must be identical to a model variable name defined above.
	 *
	 * @return array Returns an array containing DB field names.
	 */
	public function fields(){
		return [
			'gallery_id',
			'name',
			'filepath',
			'parent_id',
			'public'
		];
	}

	/**
	 * Displayable names for model variables. Each array key must be identical to a model variable name defined above. Model variables
	 * that do not have label will be displaying their variable name in the form instead.
	 *
	 * @return array Returns an associative array containing displayable names for model variables.
	 */
	public function labels(){
		return [
			'name' => 'Name',
			'parent_id' => 'Parent gallery',
			'public' => 'Public'
		];
	}

	/**
	 * Validation rules for model variables. Each key must be identical to a model variable name defined above.
	 *
	 * @return array Returns an associative array containing validation rules.
	 */
	public function rules(){
		return [
			'name' => ['required'],
			'filepath' => [['on', 'action' => 'create']]
		];
	}

	/**
	 * Pre-save operations. If model data passes validation, this action runs before DB insert or update.
	 */
	public function beforeSave(){
		// If parent is not set, set it to null value.
		if(empty($this->parent_id)) $this->parent_id = null;
	}

	/**
	 * Post-save operations. If model successfully inserted or updated, this action runs.
	 */
	public function afterSave(){
		// When new gallery is created, randomize a unique filepath name.
		if($this->isCreate()){
			$this->gallery_id = $this->db->lastInsertId();
			do {
				$path = $this->createPath();
			} while(is_dir(APPROOT . 'www/gallery/' . $path));
			mkdir(APPROOT . 'www/gallery/' . $path, 0744);
			$this->filepath = $path;
			$this->update();
		}
	}

	/**
	 * Creates a unique path name.
	 */
	private function createPath($size = 8){
		$string = 'abcdefghijklmnopqrstuvwxyz1234567890';
		$length = strlen($string) - 1;
		$name = '';
		for($i = 0; $i < $size; $i++){
			$name .= substr($string, random_int(0, $length), 1);
		}
		return $name;
	}
}
?>