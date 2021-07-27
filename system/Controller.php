<?php

class Controller {
	public $layout = DEFAULT_LAYOUT;

	public function __construct(){
		// Require controller's models.
		if(isset($this->useModels)){
			foreach($this->useModels as $thisModel){
				if(file_exists(APPROOT . 'models' . DS . $thisModel . '.php')){
					require_once(APPROOT . 'models' . DS . $thisModel . '.php');
				}
			}
		}
	}

	// Loads a view and a layout.
	public function view($view, $data = []){
		if(empty($layout)) $layout = DEFAULT_LAYOUT;
		require_once(APPROOT . DS . 'system' . DS . 'View.php');
		$view = new View($view, $data);
	}

	public function setLayout($layout){
		$this->layout = $layout;
	}

	public function getLayout(){
		return $this->layout;
	}

	public function getName(){
		return get_class($this);
	}

	public function methodPost(){
		if(Application::$app->requestMethod === "POST") return true;
		else return false;
	}

	public function methodGet(){
		if(Application::$app->requestMethod === "GET") return true;
		else return false;
	}

	public function redirect($controller, $action){
		header('location:' . URLROOT . '/' . $controller . '/' . $action);
	}
}
?>