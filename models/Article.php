<?php

class Article extends Model {

	/**
	 * Name of the associated table in DB.
	 *
	 * @return string Returns table name.
	 */
	public function tableName(){
		return 'articles';
	}

	/**
	 * Model variables, one for each DB table field, plus any other fields the form returns (but will not save to DB).
	 */
	public $article_id;
	public $user_id;
	public $caption;
	public $content;
	public $published;
	public $created;
	public $updated;

	/**
	 * Name of DB table's primary key.
	 *
	 * @return string Name of primary key.
	 */
	public function getPrimaryKey(){
		return 'article_id';
	}

	/**
	 * Names of fields in DB that can be set by model when an entry is created or updated. Each name must be identical to a model variable name defined above.
	 *
	 * @return array Returns an array containing DB field names.
	 */
	public function fields(){
		return [
			'user_id',
			'caption',
			'content',
			'published',
			'created',
			'updated'
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
			'user_id' => 'Writer',
			'caption' => 'Article name',
			'content' => 'Content',
			'published' => 'Published',
			'created' => 'Created',
			'updated' => 'Updated'
		];
	}

	/**
	 * Validation rules for model variables. Each key must be identical to a model variable name defined above.
	 *
	 * @return array Returns an associative array containing validation rules.
	 */
	public function rules(){
		return [
			'user_id' => ['required', ['on', 'action' => 'create']],
			'caption' => ['required', ['length', 'max' => 200]],
			'content' => ['required'],
			'created' => [['on', 'action' => 'create']],
			'updated' => [['on', 'action' => 'update']]
		];
	}

	/**
	 * Pre-save operations. If model data passes validation, this action runs before DB insert or update.
	 */
	public function beforeSave(){
		// When article is created, set create date.
		if($this->isCreate()){
			$this->created = date('Y-m-d H:i:s', strtotime("now"));
		}
		// When article is updated, set update date.
		else if($this->isUpdate()){
			$this->updated = date('Y-m-d H:i:s', strtotime("now"));
		}
	}

	public function afterFind(&$result){
		$userModel = new User();
		$thisUser = $userModel->findOne($result['user_id']);
		$result['author'] = $thisUser['username'];
	}
}
?>