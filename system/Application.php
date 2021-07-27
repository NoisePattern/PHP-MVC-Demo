<?php

class Application {
	public static $app;
	public $targetController = '';
	public $targetAction = '';
	public $requestMethod = '';
	public $params = [];

	public function __construct(){
		// Make application class instance reachable through a static Application::$app reference.
		self::$app = $this;
		// Start session.
		Session::start();
	}

	/**
	 * Main application execution.
	 */
	public function execute($redirect = false){
		if(!$redirect) $url = $this->handleUrl();
		// Check if url sets target controller.
		if(!empty($this->targetController)){
			// Check if controller exists.
			if(file_exists(APPROOT . 'controllers' . DS . $this->targetController . '.php')){
				require_once(APPROOT . 'controllers' . DS . $this->targetController . '.php');
				// Check if target method is not set or doesn't exist in controller. If so, go to not found page.
				if(empty($this->targetAction) || !method_exists($this->targetController, $this->targetAction)){
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
		$this->getParams();
		$this->targetController = new $this->targetController();

		// Authorization check.
		if(Auth::authorize($this->targetAction, $this->targetController->permissions)){
			call_user_func([$this->targetController, $this->targetAction], $this->params);
		} else {
			$this->targetController->redirect('Defaults', 'about');
		}
	}

	/**
	 * Gets path, request method and get params from request.
	 */
	public function handleUrl(){
		$url = parse_url(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL));
		$path = $url['path'];
		$path = trim($path, '/');
		// If path contains root directory name (because of working through localhost URL) remove it from path.
		$rootdir = basename(dirname(__DIR__));
		if(stripos($path, $rootdir) !== false){
			$path = substr($path, strlen($rootdir) + 1);
		}
		// Get controller and action.
		if(!empty($path)){
			// Take controller and action names from path.
			$path = explode('/', $path);
			$this->targetController = ucwords($path[0]);
			$this->targetAction = $path[1];
		}
	}

	/**
	 * Gets request parameters.
	 */
	public function getParams(){
		$this->params = [];
		$this->requestMethod = $_SERVER['REQUEST_METHOD'];
		// Retrieve Get-parameters.
		if($this->requestMethod === "GET"){
			foreach($_GET as $key => $value){
				$this->params[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
			}
		}
		else if($this->requestMethod === "POST"){
			foreach($_POST as $key => $value){
				$this->params[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
			}
		}
	}

	/**
	 * Sets target to landing page.
	 */
	public function targetLanding(){
		$this->targetController = LANDING_CONTROLLER;
		$this->targetAction = LANDING_METHOD;
		require_once(APPROOT . 'controllers' . DS . $this->targetController . '.php');
	}

	/**
	 * Sets target to not found page.
	 */
	public function targetNotFound(){
		$this->targetController = NOTFOUND_CONTROLLER;
		$this->targetAction = NOTFOUND_METHOD;
		require_once(APPROOT . 'controllers' . DS . $this->targetController . '.php');
	}
}
?>