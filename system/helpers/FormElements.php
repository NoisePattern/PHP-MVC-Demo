<?php

class FormElement {

	public $model;
	public $name;
	public $displayName = '';
	public $hasError = false;
	public $elements = [
		'label' => '',
		'element' => '',
		'error' => ''
	];
	public $labelClass = '';
	public $elementClass = '';
	public $wrapClass = '';
	public $hidden = false;			// Creating a hidden element. This omits other output except the element itself.
	/**
	 * Class attribute values required by by various HTML elements, as mandated by Bootstrap v5.
	 */
	public $elementDefaultClass = [
		'input' => 'form-control',				// Class required by input element of type text, email or password.
		'label' => 'form-label',				// Class required by label element.
		'checkLabel' => 'form-check-label',		// Class required be label element next to a checkbox or radio.
		'textarea' => 'form-control',			// Class required by textarea element.
		'dropdown' => 'form-select',			// Class required by select element.
		'checkbox' => 'form-check-input',		// Class required by checkbox element.
		'range' => 'form-range',				// Class required by range element.
		'radio' => 'form-check-input',			// Class required by radio element.
		'error' => 'is-invalid',				// Class required by any element with error.
		'checkDiv' => 'form-check'				// Class required by div wrapping a checkbox.
	];

	public function __construct($model, $name){
		$this->model = $model;
		$this->name = $name;
		if($model && $name){
			$this->displayName = $model->getLabel($name);
			$errorText = $model->getError($name);
			// If this model attribute has errors, the element must use error class and error message element must be generated.
			if($errorText){
				$this->hasError = true;
				// $this->setClass(['wrap' => 'error']);
				$this->error($errorText);
			}
		}
	}

	/**
	 * Magic method __toString is used to construct the final output. After the object has passed through methods in the chain
	 * and returns to an echo, it invokes this magic method to create string output.
	 */
	public function __toString(){
		$output = $this->start();
		if($this->elements['label']) $output .= $this->elements['label'];
		if($this->elements['element']) $output .= $this->elements['element'];
		if($this->elements['error']) $output .= $this->elements['error'];
		$output .= $this->end();
		return $output;
	}

	/**
	 * Creates opening div. Div wrapping can be turned off by calling wrap(false) and back on with wrap(true).
	 */
	public function start(){
		if($this->wrapClass !== false && !$this->hidden){
			return '<div class="' . $this->getClassString('wrap') . '">';
		}
		return '';
	}

	/**
	 * Creates closing div.
	 */
	public function end(){
		if($this->wrapClass !== false && $this->hidden === false){
			return '</div>';
		}
		return '';
	}

	/**
	 * Creates an input element.
	 *
	 * @param string $type The type of input field.
	 * @param array $options Array of options to format the input.
	 * - id: Sets custom id attribute to element. Default value is name from model.
	 * - class: Sets custom class value to element.
	 * - value: Sets custom value. Can be used to supply value when no model has been set. Default is model's value.
	 * - placeholder: Sets custom placeholder text. Default value is model's label text.
	 * @return object Returns object back to method chain.
	 */
	public function input($type, $options = []){
		isset($options['class']) ? $this->setClass(['element' => $options['class']]) : $this->setClass(['element' => $this->getDefaultClass('input')]);
		// Open element.
		$element = '<input type="' . $type . '" name="' . $this->name .'"';
		// Construct id attribute.
		if(isset($options['id']) && $options['id'] !== false){
			$element .= ' id ="' . $options['id'] . '"';
		} else {
			$element .= ' id="' . $this->name . '"';
		}
		// Construct class attribute.
		$element .= ' class="' . $this->getClassString('element') . '"';
		// Construct value attribute.
		$element .= ' value="' .(isset($options['value']) ? $options['value'] : $this->model->{$this->name}) . '"';
		// Construct placeholder attribute.
		if(isset($attribute['placeholder']) && $attribute['placeholder'] !== false){
			$element .= ' placholder="' . $options['placeholder'] . '"';
		} else {
			$element .= ' placeholder="' . $this->displayName . '"';
		}
		// Close element.
		$element .= '>';
		$this->elements['element'] = $element;
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates a label element.
	 *
	 * @param string|boolean $text Setting a string overrides name taken from model. Setting empty string defaults to name in model. Setting a false omits label element from output.
	 * @param array $options Array of options to format the label.
	 * - for: Sets custom for-target for the label. Default is model's name value.
	 * - class: Sets custom class value to element Default value is 'form-label'.
	 * @return object Returns object back to method chain.
	 */
	public function label($text = '', $options = []){
		// If no label is to be rendered.
		if($text === false){
			$this->element['label'] = false;
			return $this;
		}
		// Require class string based on label type.
		isset($options['class']) ? $this->setClass(['label' => $options['class']]) : $this->setClass(['label' => $this->getDefaultClass('label')]);
		// Open element.
		$label = '<label for="';
		// Construct for-label.
		$label .= (isset($options['for']) ? $options['for'] : $this->name) . '"';
		// Construct class attribute.
		$label .= ' class="' . $this->getClassString('label') . '"';
		// Label text.
		$label .= '>' . (!empty($text) ? $text : $this->displayName);
		// Close element.
		$label .= '</label>';
		$this->elements['label'] = $label;
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates a hidden input element.
	 *
	 * @param array $attributes Attributes to set to the element.
	 * - value: Sets input's value.
	 * @return object Returns object back to method chain.
	 */
	public function hidden($attributes = []){
		// Prevent label and error output on hidden elements.
		$this->elements['label'] = false;
		$this->elements['error'] = false;
		$this->hidden = true;
		$hidden = '<input type="hidden" name="' . $this->name . '"';
		if(isset($attributes['value'])){
			$hidden .= ' value="' . $attributes['value'] . '"';
		} else {
			$hidden .= ' value ="' . $this->model->{$this->name} . '"';
		}
		$hidden .= '>';
		$this->elements['element'] = $hidden;
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates a textarea element.
	 *
	 * @param array $attributes Attributes to set to the element.
	 * - id: Sets custom id attribute to element. Default value is name from model.
	 * - class: Sets custom class value to element Default value is 'form-control'.
	 * @return object Returns object back to method chain.
	 */
	public function textarea($options = []){
		isset($options['class']) ? $this->setClass(['element' => $options['class']]) : $this->setClass(['element' => $this->getDefaultClass('textarea')]);
		// Open element.
		$textarea = '<textarea name="' . $this->name .'"';
		// Construct id attribute.
		if(isset($options['id']) && $options['id'] !== false){
			$textarea .= ' id ="' . $options['id'] . '"';
		} else {
			$textarea .= ' id="' . $this->name . '"';
		}
		// Construct class attribute.
		$textarea .= ' class="' . $this->getClassString('element') . '"';
		// Content.
		$textarea .= '>' . $this->model->{$this->name};
		// Close element.
		$textarea .= '</textarea>';
		$this->elements['element'] = $textarea;
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates a dropdown element. The model contains the selected value. either as a single value or as an array, while the method receives a key-value list for dropdown content.
	 *
	 * @param array $content An associative array of key-value pairs populate dropdown options.
	 * @param array $options Array of options to format the dropdown.
	 * - id: Sets custom id attribute to element. Default value is name from model.
	 * - class: Sets custom class value to element Default value is 'form-select'.
	 * - multiple: When set to true, dropdown is made multiselect.
	 * @return object Returns object back to method chain.
	 */
	public function dropdown($content, $options = []){
		isset($options['class']) ? $this->setClass(['element' => $options['class']]) : $this->setClass(['element' => $this->getDefaultClass('dropdown')]);
		// Open element.
		$dropdown = '<select name="' . $this->name .'"';
		// Construct id attribute.
		if(isset($options['id']) && $options['id'] !== false){
			$dropdown .= ' id ="' . $options['id'] . '"';
		} else {
			$dropdown .= ' id="' . $this->name . '"';
		}
		// Construct class attribute.
		$dropdown .= ' class="' . $this->getClassString('element') . '"';
		// Multiple selection.
		if(isset($options['multiple'])){
			$dropdown .= ' multiple';
		}
		$dropdown .= '>';
		// Construct options.
		foreach($content as $key => $value){
			$dropdown .= '<option value="' . $key . '"';
			if(is_array($this->model->{$this->name})){
				if(in_array($key, $this->{$this->name})) $dropdown .= 'selected';
			} else {
				if($this->model->{$this->name} == $key) $dropdown .= ' selected';
			}
			$dropdown .= '>' . $value . '</option>';
		}
		// Close element.
		$dropdown .= '</select>';
		$this->elements['element'] = $dropdown;
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates one checkbox. Will be rendered in checked state if the value in model exists and matches the value given to checkbox.
	 *
	 * @param array options Array of options to format the checkbox.
	 * - id: Sets custom id attribute to element. Default value is name from model.
	 * - class: Sets custom class value to element. Default value is 'form-check-input'.
	 * - labelFor: Sets custom for attribute to label. Default value is checkbox id.
	 * - labelClass: Sets custom class value to label. Default is 'form-check-label'.
	 * - labelText: Sets custom text to label. Default value is model's label text.
	 * - unchecked: Sends a value in hidden field when checkbox is unchecked.
	 * - value: Sets custom value. Default value is '1'.
	 */
	public function checkbox($options = []){
		$checkbox = '';
		isset($options['class']) ? $this->setClass(['element' => $options['class']]) : $this->setClass(['element' => $this->getDefaultClass('checkbox')]);
		// Set checkbox div wrapper class.
		$this->setClass(['wrap' => $this->getDefaultClass('checkDiv')]);
		// Create unchecked hidden value's element.
		if(isset($options['unchecked'])){
			$form = new Form();
			$checkbox .= $form->using($this->model, $this->name)->hidden(['value' => $options['unchecked']]);
		}
		// Open element. Set brackets to name if serving an array of model data.
		$checkbox .= '<input type="checkbox" name="' . $this->name . (is_array($this->model->{$this->name}) ? '[]' : '') . '"';
		// Set value.
		$value = isset($options['value']) ? $options['value'] : 1;
		$checkbox .= ' value ="' . $value . '"';
		// Construct id attribute.
		if(isset($options['id']) && $options['id'] !== false){
			$checkbox .= ' id ="' . $options['id'] . '"';
		} else {
			$checkbox .= ' id="' . $this->name . '"';
		}
		// Construct class attribute.
		$checkbox .= ' class="' . $this->getClassString('element') . '"';
		// Checked state.
		if(is_array($this->model->{$this->name})){
			if(in_array($value, $this->model->{$this->name})) $checkbox .= ' checked';
		} else {
			if($this->model->{$this->name} == $value) $checkbox .= ' checked';
		}
		// Close element.
		$checkbox .= '>';
		// Attach label directly to checkbox element.
		$this->label(
			(isset($options['labelText']) ? $options['labelText'] : ''),
			['for' => (isset($options['labelFor']) ? $options['labelFor'] : $this->name), 'class' => $this->getDefaultClass('checkLabel')]
		);
		$checkbox .= $this->elements['label'];
		$this->elements['element'] = $checkbox;
		$this->elements['label'] = false;
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates multiple checkboxes. Model supplies the array of values that should be checked on the checkboxes.
	 * Checkboxes with values that match the array supplied to the method are set checked.
	 *
	 * @param array $list Array from which array keys are used as checkbox values, and array values are used as checkbox label texts.
	 * @param array $options Array of options to format the checkboxes.
	 * - class: Sets custom class value to element. Default value is 'form-check-input'.
	 * - labelClass: Sets custom class value to label. Default is 'form-check-label'.
	 * @return object Returns object back to method chain.
	 */
	public function checkboxGroup($list, $options = []){
		isset($options['class']) ? $this->setClass(['element' => $options['class']]) : $this->setClass(['element' => $this->getDefaultClass('checkbox')]);
		$this->setClass(['wrap' => $this->getDefaultClass('checkDiv')]);
		// Go through checkboxes.
		$checkbox = '';
		foreach($list as $key => $value){
			// Each checkbox must receive individual wrapping.
			$checkbox .= $this->start();
			$id = $this->name . '_' . $key;
			$sendOptions = ['id' => $id, 'labelFor' => $id, 'labelText' => $value, 'value' => $key];
			if(isset($options['class'])){
				$sendOptions['class'] = $options['class'];
			}
			if(isset($options['labelClass'])){
				$sendOptions['labelClass'] = $options['labelClass'];
			}
			$this->checkbox($sendOptions);
			$checkbox .= $this->elements['element'];
			// Bootstrap requires error messages to be within the same container that holds the form element causing it, or it will not display.
			// Because the error is for the entire checkbox group it is added only once, to the last checkbox.
			if($key == array_key_last($list) && $this->hasError){
				$checkbox .= $this->elements['error'];
				$this->unsetError();
			}
			// Close individual wrap.
			$checkbox .= $this->end();
		}
		$this->elements['element'] = $checkbox;
		// Set the default wrap class of the checkbox group to empty. Calling wrap down the method chain can be used to change this.
		$this->setClass(['wrap' => '']);
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates radio buttons. If any of the keys in given list of radios matches the value in model, it is set selected.
	 *
	 * @param array $list Array from which array keys are used as radio values, and array values are used as radio label texts.
	 * @param array $options Array of options to format the radios.
	 * - class: Sets custom class value to element. Default value is 'form-check-input'.
	 * - labelClass: Sets custom class value to label. Default is 'form-check-label'.
	 * @return object Returns object back to method chain.
	 */
	public function radio($list, $options = []){
		$this->wrap(['class' => 'form-check']);
		isset($options['class']) ? $this->setClass(['element' => $options['class']]) : $this->setClass(['element' => $this->getDefaultClass('radio')]);
		// Go through radios.
		$radio = '';
		foreach($list as $key => $value){
			// Each radio must receive individual wrapping.
			$radio .= $this->start();
			// Open element and set value.
			$radio .= '<input type="radio" name="' . $this->name . '" value ="' . $key . '"';
			// Construct id attribute.
			$id = $this->name . '_' . $key;
			$radio .= ' id="' . $id . '"';
			// Construct class attribute.
			$radio .= ' class="' . $this->getClassString('element') . '"';
			// Checked state.
			if($key == $this->model->{$this->name}) $radio .= ' checked';
			// Close element.
			$radio .= '>';
			// Attach label directly to radio element.
			$this->label($value, ['for' => $id, 'class' => (isset($options['labelClass']) ? $options['labelClass'] : $this->getDefaultClass('checkLabel'))]);
			$radio .= $this->elements['label'];
			// Close individual wrap.
			// Bootstrap requires error messages to be within the same container that holds the form element causing it, or it will not display.
			// Because the error is for the entire checkbox group it is added only once, to the last checkbox.
			if($key == array_key_last($list) && $this->hasError){
				$radio .= $this->elements['error'];
				$this->unsetError();
			}
			// Close individual wrap.
			$radio .= $this->end();
		}
		$this->elements['element'] = $radio;
		$this->elements['label'] = false;
		// Set the default wrap class of the radio group. Calling wrap down the method chain can be used to change this.
		$this->wrap(['class' => '']);
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates a range slider.
	 *
	 * @param array $options Array of options to format the range.
	 * - value: Sets custom value.
	 * - min: Sets minimum range value. Implicit default is 0.
	 * - max: Sets maximum range value. Implicit default is 100.
	 * - step: Sets step size between values. Implicit default is 1.
	 * @return object Returns object back to method chain.
	 */
	public function range($options = []){
		isset($options['class']) ? $this->setClass(['element' => $options['class']]) : $this->setClass(['element' => $this->getDefaultClass('range')]);
		// Open element.
		$range = '<input type="range" name="' . $this->name .'"';
		// Construct id attribute.
		if(isset($options['id']) && $options['id'] !== false){
			$range .= ' id ="' . $options['id'] . '"';
		} else {
			$range .= ' id="' . $this->name . '"';
		}
		// Construct class attribute.
		$range .= ' class="' . $this->getClassString('element') . '"';
		// Set min, max and step.
		if(isset($options['min'])){
			$range .= ' min="' . $options['min'] . '"';
		}
		if(isset($options['max'])){
			$range .= ' max="' . $options['max'] . '"';
		}
		if(isset($options['step'])){
			$range .= ' step="' . $options['step'] . '"';
		}
		// Set value.
		$range .= ' value="' .(isset($options['value']) ? $options['value'] : $this->model->{$this->name}) . '"';
		// Close element.
		$range .= '>';
		$this->elements['element'] = $range;
		// Return object to method chain.
		return $this;
	}

	/**
	 * Sets attributes for the wrapping div.
	 *
	 * @param array|boolean $attributes Attributes to set to the element.
	 * - Array: sets attributes from array key-value pairs. If attribute has already been set, the value is appended to existing value with space.
	 * - Boolean false: omits the wrapping div from this element. Shortcut to sending 'render' => false in array.
	 * @return object Returns object back to method chain.
	 */
	public function wrap($attributes){
		if($attributes === false){
			$this->wrapClass = false;
			return $this;
		}
		$this->setClass(['wrap' => $attributes['class']]);
		return $this;
	}

	/**
	 * Creates an error display div.
	 *
	 * @param string $error The error text to display.
	 */
	public function error($error){
		if($this->elements['error'] !== false){
			$this->elements['error'] = '<div class="invalid-feedback">' . $error . '</div>';
		}
	}

	/**
	 * Unsets error state and clears message.
	 */
	public function unsetError(){
		$this->hasError = false;
		$this->elements['error'] = '';
	}

	/**
	 * Sets an element to use a class.
	 *
	 * @param array $class An array describing the target and the class string to set.
	 * - element: the main form element's class.
	 * - label: the label's class.
	 * - wrap: the wrapper's class.
	 */
	public function setClass($class){
		if(isset($class['element'])){
			$this->elementClass = $class['element'];
		}
		else if(isset($class['label'])){
			$this->labelClass = $class['label'];
		}
		else if(isset($class['wrap'])){
			$this->wrapClass = $class['wrap'];
		}
	}

	/**
	 * Get a class string.
	 *
	 * @param string $target The source of class string.
	 * - element: the main form element's class.
	 * - label: the label's class.
	 * - wrap: the wrapper's class.
	 * @return string Returns class content string.
	 */
	public function getClassString($target){
		if($target == 'element'){
			$class = $this->elementClass;
			// Add error class if required.
			if($this->hasError){
				$class .= ' ' . $this->getDefaultClass('error');
			}
			return $class;
		}
		else if($target == 'label'){
			return $this->labelClass;
		}
		else if($target == 'wrap'){
			return $this->wrapClass;
		}
		return '';
	}

	/**
	 * Gets required class of given type.
	 *
	 * @param string $type Type of class to check.
	 * @return string Required class of of given type, empty if nothing is required.
	 */
	public function getDefaultClass($type){
		if(isset($this->elementDefaultClass[$type])){
			return $this->elementDefaultClass[$type];
		} else {
			return '';
		}
	}
}

?>