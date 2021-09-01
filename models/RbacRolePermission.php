<?php

class RbacRolePermission extends Model {

	/**
	 * Name of the associated table in DB.
	 *
	 * @return string Returns table name.
	 */
	public function tableName(){
		return 'rbac_roles_permissions';
	}

	/**
	 * Model variables, one for each DB table field, plus any other fields the form returns (but will not save to DB).
	 */
	public $rp_id;
	public $role_id;
	public $permission_id;

	/**
	 * Name of DB table's primary key.
	 *
	 * @return string Name of primary key.
	 */
	public function getPrimaryKey(){
		return 'rp_id';
	}

	/**
	 * Names of fields in DB that can be set by model when an entry is created or updated. Each name must be identical to a model variable name defined above.
	 *
	 * @return array Returns an array containing DB field names.
	 */
	public function fields(){
		return [
			'rp_id',
			'role_id',
			'permission_id'
		];
	}

	/**
	 * Displayable names for model variables. Each array key must be identical to a model variable name defined above. Model variables
	 * that do not have label will be displaying their variable name in the form instead.
	 *
	 * @return array Returns an associative array containing displayable names for model variables.
	 */
	public function labels(){
		return [];
	}

	/**
	 * Validation rules for model variables. Each key must be identical to a model variable name defined above.
	 *
	 * @return array Returns an associative array containing validation rules.
	 */
	public function rules(){
		return [
			'role_id' => ['required'],
			'permission_id' => ['required']
		];
	}

	public function relations(){
		return [];
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