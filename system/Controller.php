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

	/**
	 * Array of model names used by the controller.
	 */
	public function useModels(){
		return [];
	}

	/**
	 * Loads view and layout data.
	 */
	public function view($view, $data = []){
		require_once(APPROOT . DS . 'system' . DS . 'View.php');
		$view = new View($view, $data);
	}

	/**
	 * Sets layout to use.
	 *
	 * @param string $layout The name (without file extension) of the layout file to use. Case sensitive.
	 */
	public function setLayout($layout){
		$this->layout = $layout;
	}

	/**
	 * Gets the current layout.
	 *
	 * @return string Returns the name of layout file (without file extension). Case sensitive.
	 */
	public function getLayout(){
		return $this->layout;
	}

	/**
	 * Returns the controller's class name.
	 *
	 * @return string Returns controller class name.
	 */
	public function getName(){
		return get_class($this);
	}
}
?>