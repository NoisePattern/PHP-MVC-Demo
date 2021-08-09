<?php

class Application {
	public static $app;
	public $request;
	public $router;
	public $controller;
	public $action;
	public $params = [];

	public function __construct(){
		// Make application class instance reachable through a static Application::$app reference.
		self::$app = $this;
		// Start session.
		Session::start();
		$this->request = new Request();
		$this->router = new Router();
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
			// Authorization check.
			if(Auth::authorize($target['action'], $this->controller->permissions())){
				call_user_func([$this->controller, $target['action']]);
			} else {
				$this->request->redirect(LANDING_CONTROLLER, LANDING_METHOD);
			}
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