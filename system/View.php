<?php
require_once(APPROOT . DS . 'system' . DS . 'helpers' . DS . 'ElementConstructor.php');
require_once(APPROOT . DS . 'system' . DS . 'helpers' . DS . 'Html.php');
require_once(APPROOT . DS . 'system' . DS . 'helpers' . DS . 'Form.php');
require_once(APPROOT . DS . 'system' . DS . 'helpers' . DS . 'FormElements.php');
require_once(APPROOT . DS . 'system' . DS . 'helpers' . DS . 'Table.php');

// Main purpose of view class is to encapsulate incoming data, so the $$key = $value declarations to not clash with any controller class variables.
class View {
	public function __construct($__view, $__data){
		// Create variables from all passed data. View file will now be able to access every array index as variable name. (eg $data['user'] is now $user).
		foreach($__data as $key => $value){
			// Do not overwrite existing variables' values ($__view and $__data).
			if(!property_exists($this, $key)){
				$$key = $value;
			}
		}
		// Render view file content.
		ob_start();
		require_once(APPROOT . 'views' . DS . Application::$app->controller->getName() . DS . $__view . '.php');
		$__viewRender = ob_get_clean();
		// Render layout content and inject view to placeholder position.
		ob_start();
		require_once(APPROOT . 'views' . DS . 'layouts' . DS . Application::$app->controller->getLayout() . '.php');
		$__layoutRender = ob_get_clean();
		echo str_replace('{{viewContent}}', $__viewRender, $__layoutRender);
	}
}
?>