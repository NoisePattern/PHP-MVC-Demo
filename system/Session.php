<?php

abstract class Session {

	/**
	 * Starts session and handles queued flash messages.
	 */
	public static function start(){
		session_start();
		// Move flash messages in queue to be displayed.
		if(array_key_exists('flashMessageQueue', $_SESSION)) $_SESSION['flashMessage'] = $_SESSION['flashMessageQueue'];
		$_SESSION['flashMessageQueue'] = [];
	}


	/**
	 * Adds logged in user to session. Called from code that handles user login after a successful
	 * login attempt has been made.
	 *
	 * @param object $user Instance of user model class, filled with logged in user's data.
	 */
	public static function login($user){
		// Save user model's name to session.
		Session::setKey(['userModel' => get_class($user)]);
		// Get model's user identifying primary key name.
		$primaryKey = $user->getPrimaryKey();
		// Save user identifier value to session.
		Session::setKey(['userId' => $user->{$primaryKey}]);
	}

	/**
	 * Check login state. A user is logged in if 'userId' is set to session data.
	 */
	public static function isLogged(){
		if(Session::checkKey('userId')){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Sets one or more keys in session to given values.
	 *
	 * @param array $keys An associative array containing key-value pairs to set.
	 */
	public static function setKey($keys){
		foreach($keys as $key => $value){
			$_SESSION[$key] = $value;
		}
	}

	/**
	 * Gets given key's value in session.
	 *
	 * @param string $key Name of key to get.
	 * @return mixed|false Returns value of key or false if key does not exist.
	 */
	public static function getKey($key){
		return $_SESSION[$key] ?? false;
	}

	/**
	 * Checks that a key exists. Because a key's value may be false or null or zero, getKey() is not accurate for this check.
	 *
	 * @param string $key Name of the key to check.
	 * @return boolean Returns true if key is in session, otherwise returns false.
	 */
	public static function checkKey($key){
		if(array_key_exists($key, $_SESSION)){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Unsets a key.
	 *
	 * @param array $keys An array containing names of keys to unset.
	 */
	public static function unsetKey($keys){
		foreach($keys as $key){
			unset($_SESSION[$key]);
		}
	}
	/**
	 * Sets flash message to queue.
	 *
	 * @param string $type Type of flash message, either 'success' or 'error'.
	 * @param string $message Flash message to be displayed.
	 */
	public static function setFlash($type, $message){
		$_SESSION['flashMessageQueue'][$type] = $message;
	}

	/**
	 * Sets flash message for immediate display.
	 *
	 * @param string $type Type of flash message, either 'success' or 'error'.
	 * @param string $message Flash message to be displayed.
	 */
	public static function setFlashRender($type, $message){
		$_SESSION['flashMessage'][$type] = $message;
	}

	/**
	 * Returns displayable flash message.
	 *
	 * @param string $type Type of flash message to retrieve.
	 * @return string|false Returns message or false if none exist.
	 */
	public static function getFlash($type){
		return $_SESSION['flashMessage'][$type] ?? false;
	}
}
?>