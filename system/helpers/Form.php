<?php

class Form {

	/**
	 * Creates HTML to open a form.
	 *
	 * @param string $action Form's action.
	 * @param string $method Form's method.
	 * @param string $class Form's class, if any.
	 * @return string Returns opening form element.
	 */
	public function openForm($action, $method, $class = ''){
		// Novalidate attribute skips Bootstrap's pre-validation.
		$html = '<form action="' . $action . '" method="' . $method . '"';
		if(!empty($class)) $html .= ' class="' . $class . '"';
		$html .= ' novalidate>';
		return $html;
	}

	/**
	 * Creates HTML to close a form.
	 *
	 * @return string Returns closing form element.
	 */
	public function closeForm(){
		return '</form>';
	}

	/**
	 * Begins constructing a new form elements group. Creates and returns a form elements object to start a method chain.
	 *
	 * @param object $model The model to use to construct elements.
	 * @param string name The name of the model field to use to construct the elements.
	 */
	public function using($model, $name){
		return new FormElement($model, $name);
	}

	/**
	 * Creates HTML button element.
	 *
	 * @param string $text Text displayed on the button.
	 * @param string $type Button's type attribute. Defaults to 'button'.
	 * @param array $custom Customization rules for the element:
	 * 				'divClass' => string		Sets a class attribute to div wrapper. Set to false to omit div wrapping.
	 * 				'elementClass' => string	Sets a class attribute to button element. Uses 'btn btn-primary' if this is not set.
	 * @return string Returns a button element.
	 */
	public function button($text, $type = 'button', $custom = []){
		// Construct button.
		$elementClass = isset($custom['elementClass']) ? $custom['elementClass'] : 'btn btn-primary';
		$button = '<button type="' . $type . '" class="' . $elementClass . '">' . $text . '</button>';
		// Div wrapping.
		if(isset($custom['divClass']) && $custom['divClass'] !== false){
			// Opening div.
			$start = array_key_exists('divClass', $custom) ? '<div class="' . $custom['divClass'] . '">' : '<div>';
			// Closing div.
			$end = '</div>';
			$button = $start . $button . $end;
		} else {
			$button = '<div>' . $button . '</div>';
		}
		return $button;
	}
}
?>