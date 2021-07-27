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
	 * Creates HTML input element with associated label element, wrapped inside div element.
	 *
	 * @param string $type Input's type.
	 * @param class $model Model class this form uses. Sent by the controller's action.
	 * @param string $name Name of the model variable the input refers to.
	 * @param array $custom Customization rules for the elements:
	 * 				'divClass' => string		Sets a class attribute to div wrapper.
	 * 				'noLabel' => true			If set, no label is created.
	 * 				'labelClass' => string		Sets a class attribute to label element. Uses 'form-label' if this is not set.
	 * 				'elementClass' => string	Sets a class attribute to input element. Uses 'form-control' if this is not set.
	 * 				'placeholder' => string		Sets placeholder text to input element. Uses label text if this is not set.
	 * @return string Returns a div with label and input inside.
	 */
	public function inputField($type, $model, $name, $custom = []){
		$start = $label = $error = $end = $displayName = $errorText = '';
		if($type !== 'hidden'){
			$displayName = $model->getLabel($name);
			// Opening div.
			$start = array_key_exists('divClass', $custom) ? '<div class="' . $custom['divClass'] . '">' : '<div>';
			// Construct label.
			if(!array_key_exists('noLabel', $custom)){
				$labelClass = array_key_exists('labelClass', $custom) ? $custom['labelClass'] : 'form-label';
				$label = '<label for="' . $name . '" class="' . $labelClass . '">' . $displayName . '</label>';
			}
		}
		// Construct input element.
		$input = '<input type="' . $type . '"  id ="' . $name . '" name="' . $name . '"';
		if($type !== 'hidden'){
			$errorText = $model->getError($name);
			$elementClass = array_key_exists('elementClass', $custom) ? $custom['elementClass'] : 'form-control';
			if($errorText != false) $elementClass .= ' is-invalid';
			$input .= ' class="'. $elementClass . '"';
		}
		if(is_object($model)){
			if($model->{$name}) $input .= ' value="' . $model->{$name} . '"';
		} else {
			$input .= ' value="' . $model . '"';
		}
		if($type !== 'hidden'){
			$placeholder = array_key_exists('placeholder', $custom) ? $custom['placeholder'] : $displayName;
			$input .= ' placeholder="' . $placeholder . '"';
		}
		$input .= '>';
		if($type !== 'hidden'){
			// Construct error message.
			$error = $errorText ? '<div class="invalid-feedback">' . $errorText . '</div>' : '';
			// Closing div.
			$end = '</div>';
		}
		// Construct and return.
		return $start . $label . $input . $error . $end;
	}

	/**
	 * Creates HTML textarea element with associated label element, wrapped inside div element.
	 *
	 * @param class $model Model class this form uses. Sent by the controller's action.
	 * @param string $name Name of the model variable the input refers to.
	 * @param array $custom Customization rules for the elements:
	 * 				'divClass' => string		Sets a class attribute to div wrapper.
	 * 				'noLabel' => true			If set, no label is created.
	 * 				'labelClass' => string		Sets a class attribute to label element. Uses 'form-label' if this is not set.
	 * 				'elementClass' => string	Sets a class attribute to textarea element. Uses 'form-control' if this is not set.
	 * 				'elementId' => string		Sets an id attribure to textarea element. Uses $name if this is not set.
	 */
	public function textArea($model, $name, $custom = []){
		$displayName = $model->getLabel($name);
		$errorText = $model->getError($name);
		// Opening div.
		$start = array_key_exists('divClass', $custom) ? '<div class="' . $custom['divClass'] . '">' : '<div>';
		// Construct label.
		$label = '';
		if(!array_key_exists('noLabel', $custom)){
			$labelClass = array_key_exists('labelClass', $custom) ? $custom['labelClass'] : 'form-label';
			$label = '<label for="' . $name . '" class="' . $labelClass . '">' . $displayName . '</label>';
		}
		// Construct textarea element.
		$elementClass = array_key_exists('elementClass', $custom) ? $custom['elementClass'] : 'form-control';
		if($errorText != false) $elementClass .= ' is-invalid';
		$elementId = array_key_exists('elementId', $custom) ? $custom['elementId'] : $name;
		$textArea = '<textarea class="' . $elementClass . '" id="' . $elementId . '" name="' . $name . '">';
		if($model->{$name}) $textArea .= $model->{$name};
		$textArea .= '</textarea>';
		// Construct error message.
		$error = $errorText ? '<div class="invalid-feedback">' . $errorText . '</div>' : '';
		// Closing div.
		$end = '</div>';
		// Construct and return.
		return $start . $label . $textArea . $error . $end;
	}

	/**
	 * Creates HTML dropdown element with associated label element, wrapped inside div element.
	 *
	 * @param class $model Model class this form uses. Sent by the controller's action.
	 * @param string $name Name of the model variable the input refers to.
	 * @param array $content An associative array of keys and values to be set into dropdown.
	 * @param mixed $selectedValue The value to be set as selected option. Empty if nothing is selected.
	 * @param array $custom Customization rules for the elements:
	 * 				'divClass' => string		Sets a class attribute to div wrapper.
	 * 				'noLabel' => true			If set, no label is created.
	 * 				'labelClass' => string		Sets a class attribute to label element. Uses 'form-label' if this is not set.
	 * 				'elementClass' => string	Sets a class attribute to dropdown element. Uses 'form-control' if this is not set.
	 */
	public function dropdown($model, $name, $content, $selectedValue, $custom = []){
		$displayName = $model->getLabel($name);
		$errorText = $model->getError($name);
		// Opening div.
		$start = array_key_exists('divClass', $custom) ? '<div class="' . $custom['divClass'] . '">' : '<div>';
		// Construct label.
		$label = '';
		if(!array_key_exists('noLabel', $custom)){
			$labelClass = array_key_exists('labelClass', $custom) ? $custom['labelClass'] : 'form-label';
			$label = '<label for="' . $name . '" class="' . $labelClass . '">' . $displayName . '</label>';
		}
		// Construct dropdown element.
		$elementClass = array_key_exists('elementClass', $custom) ? $custom['elementClass'] : 'form-select';
		if($errorText != false) $elementClass .= ' is-invalid';
		$dropdown = '<select class="' . $elementClass . '" id="' . $name . '" name="' . $name . '">';
		foreach($content as $key => $value){
			$dropdown .= '<option value="' . $key . '"';
			if($selectedValue == $key) $dropdown .= ' selected';
			$dropdown .= '>' . $value . '</option>';
		}
		$dropdown .= '</select>';
		// Construct error message.
		$error = $errorText ? '<div class="invalid-feedback">' . $errorText . '</div>' : '';
		// Closing div.
		$end = '</div>';
		// Construct and return.
		return $start . $label . $dropdown . $error . $end;
	}

	/**
	 * Creates HTML button element.
	 *
	 * @param string $text Text displayed on the button.
	 * @param string $type Button's type attribute. Defaults to 'button'.
	 * @param array $custom Customization rules for the elements:
	 * 				'divClass' => string		Sets a class attribute to div wrapper.
	 * 				'noLabel' => true			If set, no label is created.
	 * 				'labelClass' => string		Sets a class attribute to label element. Uses 'form-label' if this is not set.
	 * 				'elementClass' => string	Sets a class attribute to button element. Uses 'btn btn-primary' if this is not set.
	 * @return string Returns a button element.
	 */
	public function button($text, $type = 'button', $custom = []){
		// Opening div.
		$start = array_key_exists('divClass', $custom) ? '<div class="' . $custom['divClass'] . '">' : '<div>';
		// Construct button.
		$elementClass = array_key_exists('elementClass', $custom) ? $custom['elementClass'] : 'btn btn-primary';
		$button = '<button type="' . $type . '" class="' . $elementClass . '">' . $text . '</button>';
		// Closing div.
		$end = '</div>';
		return $start . $button . $end;
	}
}
?>