<?php

class Controller {
	public $layout = DEFAULT_LAYOUT;

	public function __construct(){
		// Require controller's models.
		$models = $this->useModels();
		if(!empty($models)){
			foreach($models as $thisModel){
				if(file_exists(APPROOT . 'models' . DS . $thisModel . '.php')){
					require_once(APPROOT . 'models' . DS . $thisModel . '.php');
				}
			}
		}
	}

	public function useModels(){
		return [];
	}

	public function permissions(){
		return [];
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
}
?>