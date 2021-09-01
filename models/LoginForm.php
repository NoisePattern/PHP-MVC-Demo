<?php

class LoginForm extends Model {

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
	public $username;
	public $password;

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
			'password'
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
			'password' => 'Password',
		];
	}

	/**
	 * Validation rules for model variables. Each key must be identical to a model variable name defined above.
	 *
	 * @return array Returns an associative array containing validation rules.
	 */
	public function rules(){
		return [
			'username' => ['required'],
			'password' => ['required']
		];
	}

	public function relations(){
		return [];
	}

	public function login(){
		$user = new User();
		$data = $user->findOne(['username' => $this->username], [], PDO::FETCH_OBJ);
		if(!$data || !password_verify($this->password, $data->password)){
			return false;
		} else {
			$user->values($data);
			Session::login($user);
			return true;
		}
	}
}
?>