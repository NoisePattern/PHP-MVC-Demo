<?php

class Application {
	public static $app;
	public $request;
	public $router;
	public $user = null;
	public $controller;
	public $action;
	public $params = [];

	public function __construct(){
		// Make application class instance reachable through a static Application::$app reference.
		self::$app = $this;

		// Authorization models.
		require_once(APPROOT . 'models' . DS . 'RbacRole.php');
		require_once(APPROOT . 'models' . DS . 'RbacPermission.php');
		require_once(APPROOT . 'models' . DS . 'RbacRolePermission.php');

		// Start session.
		Session::start();
		$this->request = new Request();
		$this->router = new Router();
		$userId = Session::getKey('userId');
		// If userId has been set to session, there is a user logged in. Load the user model, fill it with logged in user's data and store it.
		if($userId){
			$userModelName = Session::getKey('userModel');
			require_once(APPROOT . 'models' . DS . $userModelName . '.php');
			$userModel = new $userModelName();
			$data = $userModel->findOne([$userModel->getPrimaryKey() => $userId], [], PDO::FETCH_ASSOC);
			$userModel->values($data);
			$this->user = $userModel;
		}
	}

	/**
	 * Main application execution.
	 */
	public function execute(){
		// Get targeted controller and action.
		$target = $this->router->getRoute($this->request->path);
		if($target){
			require_once(APPROOT . 'controllers' . DS . $target['controller'] . '.php');
			$this->controller = new $target['controller'];
			call_user_func([$this->controller, $target['action']]);
		}
		else {
			$name= NOTFOUND_CONTROLLER;
			require_once(APPROOT . 'controllers' . DS . NOTFOUND_CONTROLLER . '.php');
			$this->controller = new $name;
			call_user_func([$this->controller, NOTFOUND_METHOD]);
		}

	}
}
?>