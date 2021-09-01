<?php

class RbacRole extends Model {

	/**
	 * Name of the associated table in DB.
	 *
	 * @return string Returns table name.
	 */
	public function tableName(){
		return 'rbac_roles';
	}

	/**
	 * Model variables, one for each DB table field, plus any other fields the form returns (but will not save to DB).
	 */
	public $role_id;
	public $role_name;

	/**
	 * Name of DB table's primary key.
	 *
	 * @return string Name of primary key.
	 */
	public function getPrimaryKey(){
		return 'role_id';
	}

	/**
	 * Names of fields in DB that can be set by model when an entry is created or updated. Each name must be identical to a model variable name defined above.
	 *
	 * @return array Returns an array containing DB field names.
	 */
	public function fields(){
		return [
			'role_id',
			'role_name'
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
			'role_name' => 'Role name'
		];
	}

	/**
	 * Validation rules for model variables. Each key must be identical to a model variable name defined above.
	 *
	 * @return array Returns an associative array containing validation rules.
	 */
	public function rules(){
		return [
			'role_name' => ['required']
		];
	}


	public function relations(){
		return [
			'permissions' => ['manyToMany', 'RbacPermission', 'RbacRolePermission', 'role_id', 'permission_id']
		];
	}

	/**
	 * Pre-save operations. If model data passes validation, this action runs before DB insert or update.
	 */
	public function beforeSave(){
	}

	/**
	 * Post-save operations. If model successfully inserted or updated, this action runs.
	 */
	public function afterSave(){
	}
}
?>