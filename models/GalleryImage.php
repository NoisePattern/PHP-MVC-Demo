<?php

class GalleryImage extends Model {

	/**
	 * Name of the associated table in DB.
	 *
	 * @return string Returns table name.
	 */
	public function tableName(){
		return 'galleryimages';
	}

	/**
	 * Model variables, one for each DB table field, plus any other fields the form returns (but will not save to DB).
	 */
	public $image_id;
	public $name;
	public $filename;
	public $gallery_id;
	public $user_id;
	public $created;

	/**
	 * Name of DB table's primary key.
	 *
	 * @return string Name of primary key.
	 */
	public function getPrimaryKey(){
		return 'image_id';
	}

	/**
	 * Names of fields in DB that can be set by model when an entry is created or updated. Each name must be identical to a model variable name defined above.
	 *
	 * @return array Returns an array containing DB field names.
	 */
	public function fields(){
		return [
			'image_id',
			'name',
			'filename',
			'gallery_id',
			'user_id',
			'created'
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
			'created' => 'Created',
			'gallery_id' => 'Gallery'
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
			'filename' => ['required'],
			'gallery_id' => ['required', ['on', 'action' => 'create']],
			'user_id' => ['required', ['on', 'action' => 'create']],
			'created' => [['on', 'action' => 'create']],
		];
	}

	public function relations(){
		return [
			'gallery' => ['belongsTo', 'Gallery', 'gallery_id'],
			'owner' => ['belongsTo', 'User', 'user_id']
		];
	}
	/**
	 * Pre-save operations. If model data passes validation, this action runs before DB insert or update.
	 */
	public function beforeSave(){
		// Set image upload date.
		if($this->isCreate()){
			$this->created = date('Y-m-d H:i:s', strtotime("now"));
		}
	}

	/**
	 * Post-find operations. Runs after a select query has been executed. Called for each item in the result.
	 *
	 * @param array $result The result array of a fetch operation.
	 */
	public function afterFind(&$result){
		// If result set is empty, do nothing.
		if(empty($result)) return;
		$galleryModel = new Gallery();
		$gallery = $galleryModel->findOne($result['gallery_id']);
		$result['galleryName'] = $gallery['name'];
		$result['fullPath'] = $gallery['filepath'] . '/' . $result['filename'];
	}

	/**
	 * Creates a unique image name.
	 */
	public function createName($size = 48){
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