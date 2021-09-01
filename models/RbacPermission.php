<?php

class RbacPermission extends Model {

	/**
	 * Name of the associated table in DB.
	 *
	 * @return string Returns table name.
	 */
	public function tableName(){
		return 'rbac_permissions';
	}

	/**
	 * Model variables, one for each DB table field, plus any other fields the form returns (but will not save to DB).
	 */
	public $permission_id;
	public $permission_name;
	public $permission_description;

	/**
	 * Name of DB table's primary key.
	 *
	 * @return string Name of primary key.
	 */
	public function getPrimaryKey(){
		return 'permission_id';
	}

	/**
	 * Names of fields in DB that can be set by model when an entry is created or updated. Each name must be identical to a model variable name defined above.
	 *
	 * @return array Returns an array containing DB field names.
	 */
	public function fields(){
		return [
			'permission_id',
			'permission_name',
			'permission_description'
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
			'permission_name' => 'Permission name',
			'permission_description' => 'Permission description'
		];
	}

	/**
	 * Validation rules for model variables. Each key must be identical to a model variable name defined above.
	 *
	 * @return array Returns an associative array containing validation rules.
	 */
	public function rules(){
		return [
			'permission_name' => ['required'],
			'permission_description' => ['required']
		];
	}

	public function relations(){
		return [
			'roles' => ['manyToMany', 'RbacRole', 'RbacRolePermission', 'permission_id', 'role_id'],
			'rules' => ['manyToMany', 'RbacRule', 'RbacPermissionRule', 'permission_id', 'rule_id']
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