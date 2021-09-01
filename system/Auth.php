<?php

/**
 * To implement authorization, a controller must fill the array in permissions method. Each action that requires a permission check must be a key
 * inside the array. For each action key, an array of checks can be defined. The checks array may hold following keys:
 * - login: With value of true the user must be logged in.
 *   - example: 'login' => true;
 * - setting: Defines a key-value array where the key must be defined in session and must hold any of the array of values given.
 *   - example: 'setting' => ['level' => [3, 4, 5]];
 * Permissions are checked in definition order. If any check fails, permission is not granted.
 */

class Auth {

	/**
	 * Check if user has has given permission. Assumes getUserPermissions has been executed to populate the session with permission data.
	 *
	 * @param string $permissionName The permission to check.
	 * @return bool Returns true if user has the permission, returns false if not.
	 */
	public static function checkUserPermission($permissionName, $targetModel = null){
		if(Application::$app->user == null) return false;
		// Get user role's permissions.
		$roleModel = new RbacRole();
		$rolePermissions = $roleModel->findAll(['role_id' => Application:: $app->user->role_id], ['join' => 'permissions']);
		foreach($rolePermissions as $permission){
			if($permission['permission_name'] == $permissionName){
				return self::checkPermissionRule($permission['permission_id'], $targetModel);
			}
		}
		return false;
	}

	private static function checkPermissionRule($permissionId, $targetModel){
		$permissionModel = new RbacPermission();
		$rules = $permissionModel->findAll(['permission_id' => $permissionId], ['join' => 'rules']);
		// IF permission has no rules, just having the permission makes the check valid.
		if(empty($rules)) return true;
		foreach($rules as $rule){
			// Rule: is owner. In given model data, there must exist a column named after primary key of user model and its value must equal current user model's.
			if($rule['rule_name'] == 'isOwner'){
				if(!$targetModel) return false;
				$userKey = Application::$app->user->getPrimaryKey();
				if(!property_exists($targetModel, $userKey)) return false;
				if($targetModel->{$userKey} !== Application::$app->user->{$userKey}) return false;
				return true;
			}
		}
	}

	/**
	 * Checks through all given permissions to authorize the use of an action.
	 * @param string $action Name of the action.
	 * @param array $permissions Permissions array from the controller that owns the action.
	 * @return boolean Returns true if permitted, otherwise returns false.
	 */
/*	public static function authorize($action, $permissions){
		// If action is not defined on permissions list, grant or deny permission based on base policy.
		if(!array_key_exists($action, $permissions)){
			if(PERMISSION_STRICT){
				return false;
			} else {
				return true;
			}
		}
		foreach($permissions[$action] as $type => $value){
			// Permission check: user is logged in.
			if($type === 'login' && $value){
				if(!Session::isLogged()){
					return false;
				}
			}
			// Permission check: user data must contain a given value.
			else if($type === 'setting'){
				foreach($value as $userKey => $permittedValues){
					if(!array_key_exists($userKey, Application::$app->user)){
						return false;
					}
					$currentValue = Application::$app->user[$userKey];
					if(!in_array($currentValue, $permittedValues)){
						return false;
					}
				}
			}
		}
		// All permission checks passed.
		return true;
	}
*/
}
?>