<?php

class Application {
	public static $app;
	public $request;
	public $requestMethod = '';
	public $params = [];

	public function __construct(){
		// Make application class instance reachable through a static Application::$app reference.
		self::$app = $this;
		// Start session.
		Session::start();
		$this->request = new Request();
	}

	/**
	 * Main application execution.
	 */
	public function execute($redirect = false){
		// Check if url sets target controller.
		if(!empty($this->request->controller)){
			// Check if controller exists.
			if(file_exists(APPROOT . 'controllers' . DS . $this->request->controller . '.php')){
				require_once(APPROOT . 'controllers' . DS . $this->request->controller . '.php');
				// Check if target method is not set or doesn't exist in controller. If so, go to not found page.
				if(empty($this->request->action) || !method_exists($this->request->controller, $this->request->action)){
					$this->targetNotFound();
				}
			}
			// Given controller does not exist, go to not found page
			else {
				$this->targetNotFound();
			}
		}
		// Controller not set, go to landing page.
		else {
			$this->targetLanding();
		}

		// Instantiate controller.
		$this->request->controller = new $this->request->controller();

		// Authorization check.
		if(Auth::authorize($this->request->action, $this->request->controller->permissions)){
			call_user_func([$this->request->controller, $this->request->action], $this->request->params);
		} else {
			$this->request->redirect(LANDING_CONTROLLER, LANDING_METHOD);
		}
	}

	/**
	 * Sets target to landing page.
	 */
	public function targetLanding(){
		$this->requst->controller = LANDING_CONTROLLER;
		$this->request->action = LANDING_METHOD;
		require_once(APPROOT . 'controllers' . DS . $this->controller . '.php');
	}

	/**
	 * Sets target to not found page.
	 */
	public function targetNotFound(){
		$this->request->controller = NOTFOUND_CONTROLLER;
		$this->request->action = NOTFOUND_METHOD;
		require_once(APPROOT . 'controllers' . DS . $this->request->controller . '.php');
	}
}
?>