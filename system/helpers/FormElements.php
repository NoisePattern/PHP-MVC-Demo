<?php

class FormElement {

	public $model;
	public $name = '';
	public $displayName = '';
	public $errorText = false;
	public $elements = [
		'label' => '',
		'element' => '',
		'error' => ''
	];
	public $wrapping = [			// Attributes for div class that wraps the element.
		'render' => true			// Wrapping div is constructed by default.
	];
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
		if($this->wrapping['render'] && $this->hidden === false){
			$div = '<div';
			if(isset($this->wrapping['class'])){
				$div .= ' class="' . $this->wrapping['class'] . '"';
			}
			$div .= '>';
			return $div;
		}
		return '';
	}

	/**
	 * Creates closing div.
	 */
	public function end(){
		if($this->wrapping['render'] && $this->hidden === false){
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
	 * @param mixed $value The value submitted by the checkbox when it is selected.
	 * @param array options Array of options to format the checkbox.
	 * - id: Sets custom id attribute to element. Default value is name from model.
	 * - class: Sets custom class value to element. Default value is 'form-select'.
	 * - noWrap: If false, skips wrap setting.
	 * - labelFor: Sets custom for attribute. Default value is name from model.
	 * - labelText: Sets custom text to label. Default value is model's label text.
	 * - unchecked: Sends a value in hidden field when checkbox is unchecked.
	 */
	public function checkbox($value, $options = []){
		$checkbox = '';
		// Set checkbox div wrapper class.
		if(!isset($options['noWrap'])) $this->wrap(['class' => 'form-check']);
		// Unchecked hidden value.
		if(isset($options['unchecked'])){
			$checkbox .= $this->hidden(['value' => 0]);
			$this->hidden = false;
		}
		// Open element. Set brackets to name if serving an array of model data.
		$checkbox .= '<input type="checkbox" name="' . $this->name . (is_array($this->model->{$this->name}) ? '[]' : '') . '" value="' . $value . '"';
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
		if(isset($this->model->{$this->name})){
			if(is_array($this->model->{$this->name}) && in_array($value, $this->model->{$this->name})){
				$checkbox .= ' checked';
			} else if($this->model->{$this->name} == $value){
				$checkbox .= ' checked';
			}
		}
		// Close element.
		$checkbox .= '>';
		// Attach label directly to checkbox element.
		$this->label((isset($options['labelText']) ? $options['labelText'] : ''), ['for' => (isset($options['labelFor']) ? $options['labelFor'] : ''), 'class' => 'form-check-label']);
		$checkbox .= $this->elements['label'];
		$this->elements['label'] = '';
		$this->elements['element'] = $checkbox;
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates multiple checkboxes. Model supplies the key-value array where array keys are checkbox values and array values are checkbox labels.
	 * Checkboxes with values that match the array supplied to the method are set checked.
	 *
	 * @param array $checked Array of values that should be set checked.
	 */
	public function checkboxMulti($checked, $options = []){
		$this->wrap(['class' => 'form-check']);
		// Go through every entry in model's array.
		$checkbox = '';
		foreach($this->model->{$this->name} as $key => $value){
			// Each checkbox must receive individual wrapping.
			$checkbox .= $this->start();
			$id = $this->model->{$this->name}[$key] . '_' . $key;
			$this->checkbox($key, ['id' => $id, 'labelFor' => $id, 'labelText' => $value, 'noWrap' => true]);
			$checkbox .= $this->elements['element'];
			// Close individual wrap.
			$checkbox .= $this->end();
		}
		$this->elements['element'] = $checkbox;
		// Because every checkbox has been wrapped individually, turn off wrapping at output stage.
		$this->wrap(false);
		return $this;
	}

	/**
	 * Sets attributes for the wrapping div.
	 *
	 * @param array|boolean $attributes Attributes to set to the element.
	 * - Array: sets attributes from array key-value pairs. If attribute has aleady been set, the value is appended to existing value with space.
	 * - Boolean false: omits the wrapping div from this element. Shortcut to sending 'render' => false in array.
	 * @return object Returns object back to method chain.
	 */
	public function wrap($attributes){
		if($attributes === false){
			$this->wrapping['render'] = false;
			return;
		}
		foreach($attributes as $key => $value){
			array_key_exists($key, $this->wrapping) ? $this->wrapping[$key] .= ' ' . $value : $this->wrapping[$key] = $value;
		}
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates an error display div.
	 */
	public function error(){
		$this->elements['error'] = $error = '<div class="invalid-feedback">' . $this->errorText . '</div>';
	}
}

?>