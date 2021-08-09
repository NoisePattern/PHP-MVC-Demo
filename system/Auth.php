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
	 * Checks through all given permissions to authorize the use of an action.
	 * @param string $action Name of the action.
	 * @param array $permissions Permissions array from the controller that owns the action.
	 * @return boolean Returns true if permitted, otherwise returns false.
	 */
	public static function authorize($action, $permissions){
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
			// Permission check: user session key must contain a value.
			else if($type === 'setting'){
				foreach($value as $userKey => $permittedValues){
					// If key is not set, permission is denied.
					if(!Session::checkKey($userKey)){
						return false;
					}
					$currentValue = Session::getKey($userKey);
					if(!in_array($currentValue, $permittedValues)){
						return false;
					}
				}
			}
		}
		// All permission checks passed.
		return true;
	}
}
?>