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
	public $wrap = true;
	public $hidden = false;

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
	}

	public function start(){
		if($this->wrap !== false && $this->hidden === false){
			$div = '<div';
			if(isset($this->wrap['class'])){
				$div .= ' class="' . $this->wrap['class'] . '"';
			}
			$div .= '>';
			return $div;
		}
		return '';
	}

	public function end(){
		if($this->wrap !== false && $this->hidden === false){
			return '</div>';
		}
		return '';
	}

	/**
	 * Creates an input element.
	 *
	 * @param string $type The type of input field.
	 * @param array $attributes Attributes to set to the element. Will override default values. Setting attribute to false omits it from element (except certain required ones).
	 * @return object Returns object back to method chain.
	 */
	public function input($type, $attributes = []){
		// Open element.
		$element = '<input type="' . $type . '" name="' . $this->name .'"';
		// Construct id attribute.
		if(isset($attributes['id']) && $attributes['id'] !== false){
			$element .= ' id ="' . $attribute['id'] . '"';
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
	 * @param array $attributes Attributes to set to the element. Will override default values. Setting attribute to false omits it from element.
	 * @return object Returns object back to method chain.
	 */
	public function label($text = '', $attributes = []){
		// If no label is to be rendered.
		if($text === false){
			return $this;
		}
		// Open element.
		$label = '<label for="' . $this->name . '"';
		// Construct class attribute.
		if(isset($attributes['class']) && $attributes['class'] !== false){
			$label .= ' class="' . $attributes['class'] . '"';
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
	 * Creates a dropdown element.
	 *
	 * @param array $content An associative array of key-value pairs populate dropdown options.
	 * @param mixed $selected Key from content array that should be selected in dropdown. Set to false to select nothing. Defaults to false.
	 * @param array $attributes Attributes to set to the element.
	 * @return object Returns object back to method chain.
	 */
	public function dropdown($content, $selected = false, $attributes = []){
		// Open element.
		$dropdown = '<select name="' . $this->name .'"';
		// Construct id attribute.
		if(isset($attributes['id']) && $attributes['id'] !== false){
			$dropdown .= ' id ="' . $attributes['id'] . '"';
		} else {
			$dropdown .= ' id="' . $this->name . '"';
		}
		// Construct class attribute.
		if(isset($attributes['class']) && $attributes['class'] !== false){
			$dropdown .= ' class="' . $attributes['class'];
			if($this->errorText !== false) $dropdown .= ' is-invalid';
			$dropdown .= '"';
		} else {
			$dropdown .= ' class="form-select';
			if($this->errorText !== false) $dropdown .= ' is-invalid';
			$dropdown .= '"';
		}
		$dropdown .= '>';
		// Construct options.
		foreach($content as $key => $value){
			$dropdown .= '<option value="' . $key . '"';
			if($selected == $key) $dropdown .= ' selected';
			$dropdown .= '>' . $value . '</option>';
		}
		// Close element.
		$dropdown .= '</select>';
		$this->elements['element'] = $dropdown;
		// Return object to method chain.
		return $this;	}

	/**
	 * Sets attributes for the wrapping div.
	 *
	 * @param array|boolean $attributes Attributes to set to the element. Setting to false omits the wrapping div.
	 */
	public function wrap($attributes){
		$this->wrap = $attributes;
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