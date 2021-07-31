<?php

class FormElement {

	public $model;
	public $name;
	public $displayName = '';
	public $errorText = false;
	public $elements = [
		'label' => '',
		'element' => '',
		'error' => ''
	];
	public $wrapClass = '';
	public $hidden = false;			// Creating a hidden element. This omits other output except the element itself.

	public function __construct($model, $name){
		$this->model = $model;
		$this->name = $name;
		if($model && $name){
			$this->displayName = $model->getLabel($name);
			$this->errorText = $model->getError($name);
		}
	}

	/**
	 * Magic method __toString is used to construct the final output. After the object has passed through methods in the chain
	 * and returns to an echo, it invokes this magic method to create string output.
	 */
	public function __toString(){
		if($this->errorText){
			$this->error();
		}
		return $this->start() . $this->elements['label'] . $this->elements['element'] . $this->elements['error'] . $this->end();
		$this->wrapping['render'] = true;
	}

	/**
	 * Creates opening div. Div wrapping can be turned off by calling wrap(false) and back on with wrap(true).
	 */
	public function start(){
		if($this->wrapClass !== false && $this->hidden === false){
			return '<div class="' . $this->wrapClass . '">';
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
	 * @param array $attributes Attributes to set to the element.
	 * - id: Sets custom id attribute to element. Default value is name from model.
	 * - class: Sets custom class value to element Default value is 'form-control'.
	 * - value: Sets custom value. Can be used to supply value when no model has been set. Default is model's value.
	 * - placeholder: Sets custom placeholder text. Default value is model's label text.
	 * @return object Returns object back to method chain.
	 */
	public function input($type, $attributes = []){
		// Open element.
		$element = '<input type="' . $type . '" name="' . $this->name .'"';
		// Construct id attribute.
		if(isset($attributes['id']) && $attributes['id'] !== false){
			$element .= ' id ="' . $attributes['id'] . '"';
		} else {
			$element .= ' id="' . $this->name . '"';
		}
		// Construct class attribute.
		if(isset($attributes['class']) && $attributes['class'] !== false){
			$element .= ' class="' . $attributes['class'];
			if($this->errorText !== false) $element .= ' is-invalid';
			$element .= '"';
		} else {
			$element .= ' class="form-control';
			if($this->errorText !== false) $element .= ' is-invalid';
			$element .= '"';
		}
		// Construct value attribute.
		$element .= ' value="' .(isset($attributes['value']) ? $attributes['value'] : $this->model->{$this->name}) . '"';
		// Construct placeholder attribute.
		if(isset($attribute['placeholder']) && $attribute['placeholder'] !== false){
			$element .= ' placholder="' . $attributes['placeholder'] . '"';
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
	 * @param array $options Attributes to set to the element. Will override default values. Setting attribute to false omits it from element.
	 * - for: Sets custom for-target for the label. Default is model's name value.
	 * - class: Sets custom class value to element Default value is 'form-label'.
	 * @return object Returns object back to method chain.
	 */
	public function label($text = '', $options = []){
		// If no label is to be rendered.
		if($text === false){
			return $this;
		}
		// Open element.
		$label = '<label for="';
		// Construct for-label.
		$label .= (isset($options['for']) ? $options['for'] : $this->name) . '"';
		// Construct class attribute.
		if(isset($options['class']) && $options['class'] !== false){
			$label .= ' class="' . $options['class'] . '"';
		} else {
			$label .= ' class="form-label' . '"';
		}
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
	public function textarea($attributes){
		// Open element.
		$textarea = '<textarea name="' . $this->name .'"';
		// Construct id attribute.
		if(isset($attributes['id']) && $attributes['id'] !== false){
			$textarea .= ' id ="' . $attributes['id'] . '"';
		} else {
			$textarea .= ' id="' . $this->name . '"';
		}
		// Construct class attribute.
		if(isset($attributes['class']) && $attributes['class'] !== false){
			$textarea .= ' class="' . $attributes['class'];
			if($this->errorText !== false) $textarea .= ' is-invalid';
			$textarea .= '"';
		} else {
			$textarea .= ' class="form-control';
			if($this->errorText !== false) $textarea .= ' is-invalid';
			$textarea .= '"';
		}
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
		// Open element.
		$dropdown = '<select name="' . $this->name .'"';
		// Construct id attribute.
		if(isset($options['id']) && $options['id'] !== false){
			$dropdown .= ' id ="' . $options['id'] . '"';
		} else {
			$dropdown .= ' id="' . $this->name . '"';
		}
		// Construct class attribute.
		if(isset($options['class']) && $options['class'] !== false){
			$dropdown .= ' class="' . $options['class'];
			if($this->errorText !== false) $dropdown .= ' is-invalid';
			$dropdown .= '"';
		} else {
			$dropdown .= ' class="form-select';
			if($this->errorText !== false) $dropdown .= ' is-invalid';
			$dropdown .= '"';
		}
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
	 * - labelFor: Sets custom for attribute. Default value is name from model.
	 * - labelClass: Sets custom class value to label. Default is 'form-check-label'.
	 * - labelText: Sets custom text to label. Default value is model's label text.
	 * - unchecked: Sends a value in hidden field when checkbox is unchecked.
	 * - value: Sets custom value. Default value is '1'.
	 */
	public function checkbox($options = []){
		$checkbox = '';
		// Set checkbox div wrapper class.
		$this->wrap(['class' => 'form-check']);
		// Unchecked hidden value.
		if(isset($options['unchecked'])){
			$checkbox .= $this->hidden(['value' => $options['unchecked']]);
			$this->hidden = false;
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
		if(isset($options['class']) && $options['class'] !== false){
			$checkbox .= ' class="' . $options['class'];
			if($this->errorText !== false) $checkbox .= ' is-invalid';
			$checkbox .= '"';
		} else {
			$checkbox .= ' class="form-check-input';
			if($this->errorText !== false) $checkbox .= ' is-invalid';
			$checkbox .= '"';
		}
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
			['for' => (isset($options['labelFor']) ? $options['labelFor'] : $this->name), 'class' => (isset($options['labelClass']) ? $options['labelClass'] : 'form-check-label')]
		);
		$checkbox .= $this->elements['label'];
		$this->elements['label'] = '';
		$this->elements['element'] = $checkbox;
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
		$this->wrap(['class' => 'form-check']);
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
			if($key == array_key_last($list) && $this->errorText){
				$this->error();
				$checkbox .= $this->elements['error'];
				$this->unsetError();
			}
			// Close individual wrap.
			$checkbox .= $this->end();
		}
		$this->elements['element'] = $checkbox;
		// Set the default wrap class of the checkbox group. Calling wrap down the method chain can be used to change this.
		$this->wrap(['class' => '']);
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
			if(isset($options['class']) && $options['class'] !== false){
				$radio .= ' class="' . $options['class'];
				if($this->errorText !== false) $radio .= ' is-invalid';
				$radio .= '"';
			} else {
				$radio .= ' class="form-check-input';
				if($this->errorText !== false) $radio .= ' is-invalid';
				$radio .= '"';
			}
			// Checked state.
			if($key == $this->model->{$this->name}) $radio .= ' checked';
			// Close element.
			$radio .= '>';
			// Attach label directly to radio element.
			$this->label($value, ['for' => $id, 'class' => (isset($options['labelClass']) ? $options['labelClass'] : 'form-check-label')]);
			$radio .= $this->elements['label'];
			// Close individual wrap.
			$radio .= $this->end();
		}
		$this->elements['element'] = $radio;
		$this->elements['label'] = '';
		// Set the wrap of the radio group.
		if(isset($options['wrapClass'])){
			if(!$options['wrapClass']) $this->wrap(false);
			else $this->wrap(['class' => $options['wrapClass']]);
		} else {
			$this->wrap(['class' => '']);
		}
		// Set the default wrap class of the radio group. Calling wrap down the method chain can be used to change this.
		$this->wrap(['class' => '']);
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
		if(isset($attributes['class'])){
			$this->wrapClass = $attributes['class'];
		}
		else if(isset($attributes['addClass'])){
			$this->wrapClass = $this->wrapClass . ' ' . $attributes['addClass'];
		}
		return $this;
	}

	/**
	 * Creates an error display div.
	 */
	public function error(){
		$this->elements['error'] = '<div class="invalid-feedback">' . $this->errorText . '</div>';
	}

	/**
	 * Unsets error state and clears message.
	 */
	public function unsetError(){
		$this->errorText = false;
		$this->elements['error'] = '';
	}
}

?>