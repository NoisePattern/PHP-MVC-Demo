<?php

class User extends Model {

	/**
	 * Name of the associated table in DB.
	 *
	 * @return string Returns table name.
	 */
	public function tableName(){
		return 'users';
	}

	/**
	 * Model variables, one for each DB table field, plus any other fields the form returns (but will not save to DB).
	 */
	public $user_id;
	public $username;
	public $email;
	public $role_id;
	public $password;
	public $confirmPassword;
	public $created;
	public $updated;

	/**
	 * Name of DB table's primary key.
	 *
	 * @return string Name of primary key.
	 */
	public function getPrimaryKey(){
		return 'user_id';
	}

	/**
	 * Names of fields in DB that can be set by model when an entry is created or updated. Each name must be identical to a model variable name defined above.
	 *
	 * @return array Returns an array containing DB field names.
	 */
	public function fields(){
		return [
			'username',
			'email',
			'role_id',
			'password',
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
			'username' => 'Username',
			'email' => 'Email',
			'role_id' => 'User role',
			'password' => 'Password',
			'confirmPassword' => 'Password Confirm',
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
			'username' => ['required', 'unique', ['length' , 'max' => 255]],
			'email' => ['required', 'unique', 'email'],
			'role_id' => ['required'],
			'password' => ['required', ['length', 'min' => 5]],
			'confirmPassword' => ['required', ['compare', 'field' => 'password']],
			'created' => [['on', 'action' => 'create']],
			'updated' => [['on', 'action' => 'update']]
		];
	}

	public function relations(){
		return [
			'ownArticles' => ['hasMany', 'Article', 'article_id'],
			'ownImages' => ['hasMany', 'GalleryImage', 'image_id'],
			'role' => ['hasOne', 'RbacRole', 'role_id']
		];
	}

	/**
	 * Pre-save operations. If model data passes validation, this action runs before DB insert or update.
	 */
	public function beforeSave(){
		// Hash the password.
		$this->password = password_hash($this->password, PASSWORD_DEFAULT);
		// When user is created, set create date.
		if($this->isCreate()){
			$this->created = date('Y-m-d H:i:s', strtotime("now"));
		}
		// When user data is updated, set update date.
		else if($this->isUpdate()){
			$this->updated = date('Y-m-d H:i:s', strtotime("now"));
		}
	}

	/**
	 * Post-save operations. If model successfully inserted or updated, this action runs.
	 */
	public function afterSave(){
	}
}
?>