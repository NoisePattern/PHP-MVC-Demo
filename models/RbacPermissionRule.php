<?php

class RbacPermissionRule extends Model {

	/**
	 * Name of the associated table in DB.
	 *
	 * @return string Returns table name.
	 */
	public function tableName(){
		return 'rbac_permissions_rules';
	}

	/**
	 * Model variables, one for each DB table field, plus any other fields the form returns (but will not save to DB).
	 */
	public $pr_id;
	public $permission_id;
	public $rule_id;

	/**
	 * Name of DB table's primary key.
	 *
	 * @return string Name of primary key.
	 */
	public function getPrimaryKey(){
		return 'pr_id';
	}

	/**
	 * Names of fields in DB that can be set by model when an entry is created or updated. Each name must be identical to a model variable name defined above.
	 *
	 * @return array Returns an array containing DB field names.
	 */
	public function fields(){
		return [];
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
		return [];
	}

	/**
	 * Table's foreign key relations. An array where each key is name of the field and value is an array where related table is first and field second.
	 *
	 * @return array Returns array of foreign key relations.
	 */
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