<?php

class Request {

	public $host = '';			// Current domain.
	public $path = '';			// Current path.
	public $controller;			// Controller name from path.
	public $action = '';		// Action name from path.
	public $method = '';		// Request method, either GET or POST.
	public $getParams = [];		// GET request params.
	public $postParams = [];	// POST request params.

	public function __construct(){
		$this->handleUrl();
		$this->handleParams();
	}

	/**
	 * Reads the target controller and action from request URL.
	 */
	public function handleUrl(){
		$this->host = filter_var($_SERVER['HTTP_HOST'], FILTER_SANITIZE_URL);
		$this->path = parse_url(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL), PHP_URL_PATH);
		$this->path = trim($this->path, '/');
		$this->path = $this->removeRoot($this->path);
		$route = $this->splitPath($this->path);
		if($route !== false){
			$this->controller = ucwords($route[0]);
			$this->action = $route[1];
		}
	}

	/**
	 * If path contains root directory name (because of working through localhost URL) remove it from path.
	 */
	function removeRoot($path){
		$rootdir = basename(dirname(__DIR__));
		if(stripos($path, $rootdir) !== false){
			return substr($path, strlen($rootdir) + 1);
		} else {
			return $path;
		}

	}

	/**
	 * Get controller and action names.
	 */
	function splitPath($path){
		if(!empty($this->path)){
			$names = explode('/', $this->path);
			return $names;
		} else {
			return false;
		}

	}

	/**
	 * Gets request method and parameters.
	 */
	public function handleParams(){
		$this->params = [];
		$this->method = $_SERVER['REQUEST_METHOD'];
		// Retrieve Get-parameters.
		if($this->method === "GET"){
			foreach($_GET as $key => $value){
				$this->getParams[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
			}
		}
		else if($this->method === "POST"){
			foreach($_POST as $key => $value){
				if(is_array($value)){
					array_walk_recursive($value, function(&$attr){
						$attr = filter_var($attr, FILTER_SANITIZE_SPECIAL_CHARS);
					});
					$this->postParams[$key] = $value;
				} else {
					$this->postParams[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
				}
			}
		}
	}

	/**
	 * Returns GET params array.
	 */
	public function get(){
		return $this->getParams;
	}

	/**
	 * Returns POST params array.
	 */
	public function post(){
		return $this->postParams;
	}

	/**
	 * Checks if request was POST type.
	 *
	 * @return boolean Returns true if request was POST, otherwise returns false.
	 */
	public function isPost(){
		return $this->method === "POST";
	}

	/**
	 * Checks if request was GET type.
	 *
	 * @return boolean Returns true if request was GET, otherwise returns false.
	 */
	public function isGet(){
		return $this->method === "GET";
	}

	/**
	 * Gets the previous page if it was on same domain.
	 *
	 * @return array Returns an array where 'path' is the full path and 'controller' and 'action' contain said fragments, or return false if not on same domain.
	 */
	public function getReferer(){
		if(!empty($_SERVER['HTTP_REFERER'])){
			$url = filter_var($_SERVER['HTTP_REFERER'], FILTER_SANITIZE_URL);
			$host = parse_url($url, PHP_URL_HOST);
			if($host === $this->host){
				$path = parse_url($url, PHP_URL_PATH);
				$path = trim($path, '/');
				$path = $this->removeRoot($path);
				$names = explode('/', $path);
				$data['path'] = $path;
				$data['controller'] = $names[0];
				$data['action'] = $names[1];
				return $data;
			}
		}
		return false;
	}

	/**
	 * Redirects to target page.
	 */
	public function redirect($controller, $action){
		header('location:' . URLROOT . '/' . $controller . '/' . $action);
	}
}
?>