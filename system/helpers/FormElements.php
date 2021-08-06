<?php

class FormElement {
	public $DOM;
	public $model;
	public $name;
	public $displayName = '';
	public $useDefaults = true;					// Use Bootstrap classes as default class values for elements.
	public $hasError = false;					// Error message exists to be displayed.
	public $errorClasses = true;				// Use error classes on elements.
	public $errorMessages = true;				// Display error message containers.
	public $elementOrder = [					// Default element construction order.
		'label', 'control' , 'error'
	];
	public $elements = [						// Storage for constructed form elements.
		'wrap' => '',
		'label' => '',
		'control' => '',
		'error' => ''
	];
	public $wrapAttributes = [];
	public $elementDefaultClass = [				// Class attribute values required by by various HTML elements, as mandated by Bootstrap v5.
		'input' => 'form-control',				// Class required by input element of type text, email or password.
		'label' => 'form-label',				// Class required by label element.
		'checkLabel' => 'form-check-label',		// Class required be label element next to a checkbox or radio.
		'textarea' => 'form-control',			// Class required by textarea element.
		'select' => 'form-select',				// Class required by select element.
		'checkbox' => 'form-check-input',		// Class required by checkbox element.
		'range' => 'form-range',				// Class required by range element.
		'radio' => 'form-check-input',			// Class required by radio element.
		'button' => 'btn btn-primary',			// Class required by button element.
		'error' => 'is-invalid',				// Class required by any element with error.
		'checkDiv' => 'form-check',				// Class required by div wrapping a checkbox.
		'errorDiv' => 'invalid-feedback'		// Class required by div wrapping an error message.
	];

	public function __construct($model, $name, $options = []){
		$this->DOM = new ElementConstructor();
		$this->model = $model;
		$this->name = $name;
		if(isset($options['elementOrder'])){
			$this->elementOrder = $options['elementOrder'];
		}
		if(isset($options['noDefault'])){
			$this->useDefaults = false;
		}
		if(isset($options['noErrorClass'])){
			$this->errorClass = false;
		}
		if(isset($options['noErrorMessage'])){
			$this->errorMessages = false;
		}
		if($model && $name){
			$this->displayName = $model->getLabel($name);
			$errorText = $model->getError($name);
			// If this model attribute has errors, the element must use error class and error message element must be generated.
			if($errorText){
				$this->hasError = true;
				$this->error($errorText);
			}
		}
	}

	/**
	 * Magic method __toString is used to construct the final output. After the object has passed through chained methods
	 * and returns to a string manipulator, such as an echo, it invokes this magic method to create string output.
	 */
	public function __toString(){
		return $this->combineElements();
	}

	/**
	 * Combines elements into a single unit.
	 */
	public function combineElements(){
		// If label has been set to be omitted, skip.
		if($this->elements['label'] !== false){
			// If label has not yet been created, create a default format wrapper.
			if($this->elements['label'] === ''){
				$this->elements['label'] = Html::label($this->displayName, $this->name, ['class' => $this->getDefaultClass('label')]);
			}
		}
		$html = '';
		foreach($this->elementOrder as $order){
			if($this->elements[$order] !== false && $order !== 'wrap'){
				$html .=$this->elements[$order];
			}
		}
		// If wrapper has been set to be omitted, skip.
		if($this->elements['wrap'] !== false){
			$html = Html::div('', $html, $this->wrapAttributes);
		}
		return $html;
	}

	/**
	 * Creates an input element.
	 *
	 * @param string $type The type of input element.
	 * - text: a text input.
	 * - email: an email input.
	 * - password: a password input.
	 * @param array $options Array of options as key-value pairs to format the element. Supported keys:
	 * - id: Sets custom id attribute to element. Default value is name from model. If set, must also be set as label's for-attribute.
	 * - class: Sets custom class value to element. Default value is 'form-control' if defaults are in use, none otherwise.
	 * - value: Sets custom value. Can be used to supply value when no model has been set. Default is model's value.
	 * - placeholder: Sets custom placeholder text. Default value is model's label text.
	 * @return object Returns object back to method chain.
	 */
	public function input($type, $options = []){
		// Set default attributes.
		Html::setAttribute($options, [
			'id' => $this->name,
			'class' => $this->getDefaultClass('input'),
			'placeholder' => $this->displayName
		]);
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			Html::addToAttribute($options, 'class', $this->getDefaultClass('error'));
		}
		// Create and store the element.
		$this->elements['control'] = Html::input(
			$type,
			$this->name,
			Html::selectAttribute('value', $options, $this->model->{$this->name}),
			$options
		);
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates a label element.
	 *
	 * @param string|boolean $text Setting a string overrides name taken from model. Setting empty string defaults to name in model. Setting a false omits label element from output.
	 * @param array $options Array of options as key-value pairs to format the element. Supported keys:
	 * - for: Sets custom for-target for the label. Default is model's name value.
	 * - class: Sets custom class value to element. Default value is 'form-label' if defaults are in use, none otherwise.
	 * @return object Returns object back to method chain.
	 */
	public function label($text = '', $options = []){
		// If no label is to be rendered.
		if($text === false){
			$this->elements['label'] = false;
			return $this;
		}
		Html::setAttribute($options, ['class' => $this->getDefaultClass('label')]);
		// Create and store the element.
		$this->elements['label'] = Html::label(
			!$text === '' ? $text : $this->displayName,
			Html::selectAttribute('for', $options, $this->name),
			$options
		);
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates a hidden input element.
	 *
	 * @param array $options Array of options as key-value pairs to format the element. Supported keys:
	 * - id: Sets custom id attribute to element. Default value is name from model.
	 * - value: Sets custom value. Default value is model's value.
	 * @return object Returns object back to method chain.
	 */
	public function hidden($options = []){
		$this->elements['label'] = false;
		$this->elements['error'] = false;
		$this->elements['wrap'] = false;
		// Set default attributes.
		Html::setAttribute($options, ['id' => $this->name]);
		// Create and store the element.
		$this->elements['control'] = Html::input(
			'hidden',
			$this->name,
			Html::selectAttribute('value', $options, $this->model->{$this->name}),
			$options
		);
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates a textarea element.
	 *
	 * @param array $options Array of options as key-value pairs to format the element. Supported keys:
	 * - id: Sets custom id attribute to element. Default value is name from model. If set, must also be set as label's for-attribute.
	 * - class: Sets custom class value to element. Default value is 'form-control' if defaults are in use, none otherwise.
	 * @return object Returns object back to method chain.
	 */
	public function textarea($options = []){
		// Set default attributes.
		Html::setAttribute($options, [
			'id' => $this->name,
			'class' => $this->getDefaultClass('input')
		]);
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			Html::addToAttribute($options, 'class', $this->getDefaultClass('error'));
		}
		// Create and store the element.
		$this->elements['control'] = Html::textarea(
			html_entity_decode($this->model->{$this->name}),
			$this->name,
			$options
		);
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates a select element. The model contains the selected value, either as a single value or as an array, while the method receives a key-value list of select content.
	 *
	 * @param array $content An array of key-value pairs to populate select options.
	 * @param array $options Array of options as key-value pairs to format the element. Supported keys:
	 * - id: Sets custom id attribute to element. Default value is name from model. If set, must also be set as label's for-attribute.
	 * - class: Sets custom class value to element. Default value is 'form-select' if defaults are in use, none otherwise.
	 * - multiple: When set to true, select is made multiselect.
	 * @return object Returns object back to method chain.
	 */
	public function select($content, $options = []){
		// Set default attributes.
		Html::setAttribute($options, [
			'id' => $this->name,
			'class' => $this->getDefaultClass('select')
		]);
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			Html::addToAttribute($options, 'class', $this->getDefaultClass('error'));
		}
		// Create and store the element.
		$this->elements['control'] = Html::select(
			$content,
			$this->model->{$this->name},
			$this->name,
			$options
		);
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates one checkbox. Will be rendered in checked state if the value in model exists and matches the value given to checkbox.
	 *
	 * @param array options Array of options as key-value pairs to format the element. Supported keys:
	 * - id: Sets custom id attribute to element. Default value is name from model. If set, must also be set as label's for-attribute.
	 * - class: Sets custom class value to element. Default value is 'form-check-input' if defaults are in use, none otherwise.
	 * - value: Sets custom value. Default value is '1'.
	 * - unchecked: Value to be returned (in hidden field) if checkbox is unchecked.
	 * - grouped: Is set, checkbox is selecting from a group of options so [] is added to element name.
	 */
	public function checkbox($options = []){
		$value = Html::selectAttribute('value', $options, '1');
		// Set default attributes.
		Html::setAttribute($options, [
			'id' => $this->name,
			'class' => $this->getDefaultClass('checkbox'),
		]);
		// Checked state.
		if($this->checkModelValue($value, $this->name)) Html::setAttribute($options, ['checked' => 'checked']);
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			Html::addToAttribute($options, 'class', $this->getDefaultClass('error'));
		}
		// Create unchecked hidden value's element.
		$hidden = '';
		if(isset($options['unchecked'])){
			$hidden = Html::input('hidden', $this->name, $options['unchecked']);
			unset($options['unchecked']);
		}
		// Create and store the element.
		$this->elements['control'] = Html::checkbox(
			$this->name,
			$value,
			$options
		) . $hidden;
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates multiple checkboxes. Model supplies the array of values that should be checked on the checkboxes.
	 * Checkboxes with values that match the array supplied to the method are set checked.
	 *
	 * @param array $list Array from which array keys are used as checkbox values, and array values are used as checkbox label texts.
	 * @param array options Array of options as key-value pairs to format the element. Supported keys:
	 * - class: Sets custom class value to checkboxes. Default value is 'form-check-input' if defaults are in use, none otherwise.
	 * - labelClass: Sets custom class value to labels. Default value is 'form-check-label' if defaults are in use, none otherwise.
	 * - wrapClass: Sets custom class value to checkbox wrapper. Default value is 'form-check' if defaults are in use, none otherwise.
	 * @return object Returns object back to method chain.
	 */
	public function checkboxGroup($list, $options = []){
		$group = '';
		// Loop through content.
		foreach($list as $key => $value){
			// Create checkbox.
			$id = $this->name . '_' . $key;
			$elementOptions = [
				'id' => $id,
				'class' => Html::selectAttribute('class', $options, $this->getDefaultClass('checkbox'))
			];
			if($this->checkModelValue($key, $this->name)) $elementOptions['checked'] = 'checked';
			$checkbox = Html::checkbox($this->name . '[]', $key, $elementOptions);
			// Create label.
			$labelOptions = ['class' => Html::selectAttribute('labelClass', $options, $this->getDefaultClass('checkLabel'))];
			$label = Html::label($value, $id, $labelOptions);
			// Create and fill div wrapper.
			$div = Html::div('', $checkbox . $label, ['class' => Html::selectAttribute('wrapClass', $options, $this->getDefaultClass('checkDiv'))]);
			$group .= $div;
		}
		// Store element group.
		$this->elements['control'] = $group;
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates multiple radios. Model supplies the value that is set selected.
	 *
	 * @param array $list Array from which array keys are used as radio values, and array values are used as radio label texts.
	 * @param array $options Array of options as key-value pairs to format the element. Supported keys:
	 * - class: Sets custom class value to element. Default value is 'form-check-input' if defaults are in use, none otherwise.
	 * - labelClass: Sets custom class value to label. Default is 'form-check-label' if defaults are in use, none otherwise.
	 * - wrapClass: Sets custom class to div that wraps radio and label. Default is 'form-check' if defaults are in use, none otherwise.
	 * @return object Returns object back to method chain.
	 */
	public function radioGroup($list, $options = []){
		$group = '';
		// Loop through content.
		foreach($list as $key => $value){
			// Create radio.
			$id = $this->name . '_' . $key;
			$elementOptions = [
				'id' => $id,
				'class' => Html::selectAttribute('class', $options, $this->getDefaultClass('radio'))
			];
			if($this->checkModelValue($key, $this->name)) $elementOptions['checked'] = 'checked';
			$radio = Html::radio($this->name . '[]', $key, $elementOptions);
			// Create label.
			$labelOptions = ['class' => Html::selectAttribute('labelClass', $options, $this->getDefaultClass('checkLabel'))];
			$label = Html::label($value, $id, $labelOptions);
			// Create and fill div wrapper.
			$div = Html::div('', $radio . $label, ['class' => Html::selectAttribute('wrapClass', $options, $this->getDefaultClass('checkDiv'))]);
			$group .= $div;
		}
		// Store element group.
		$this->elements['control'] = $group;
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates a wrapping div element.
	 *
	 * @param array $options Array of options as key-value pairs to format the element. Supported keys:
	 * - class: Sets custom class value to element. Default value is no class.
	 * @return object Returns object back to method chain.
	 */
	public function wrap($options = []){
		if(isset($options['noWrap'])){
			$this->elements['wrap'] = false;
			return $this;
		}
		$this->wrapAttributes = $options;
		return $this;
	}

	/**
	 * Creates a range slider.
	 *
	 * @param array $options Array of options as key-value pairs to format the element. Supported keys:
	 * - id: Sets custom id attribute to element. Default value is name from model. If set, must also be set as label's for-attribute.
	 * - class: Sets custom class value to element. Default value is 'form-control' if defaults are in use, none otherwise.
	 * - value: Sets custom value. Can be used to supply value when no model has been set. Default is model's value.
	 * - min: Sets minimum range value. Implicit default is 0.
	 * - max: Sets maximum range value. Implicit default is 100.
	 * - step: Sets step size between values. Implicit default is 1.
	 * @return object Returns object back to method chain.
	 */
	public function range($options = []){
		// Set default attributes.
		Html::setAttribute($options, [
			'id' => $this->name,
			'class' => $this->getDefaultClass('range')
		]);
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			Html::addToAttribute($options, 'class', $this->getDefaultClass('error'));
		}
		// Create and store the element.
		$this->elements['control'] = Html::range(
			$this->name,
			Html::selectAttribute('value', $options, $this->model->{$this->name}),
			$options
		);
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates a button element.
	 *
	 * @param string $type Type of button element.
	 * @param string $text Text displayed on the button.
	 * @param array $options Array of options as key-value pairs to format the element.
	 */
	public function button($type, $text, $options = []){
		// Set default attributes.
		Html::setAttribute($options, [
			'class' => $this->getDefaultClass('button')
		]);
		// Create and store the element.
		$this->elements['control'] = Html::button(
			$type,
			$text,
			$options
		);
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates an error display div.
	 *
	 * @param string $error The error text to display.
	 */
	public function error($error){
		$options = ['class' => $this->getDefaultClass('errorDiv')];
		$this->elements['error'] = Html::div($error, '', $options);
	}

	/**
	 * Selects an attribute value from given options.
	 *
	 * @param string $attribute name of the attribute.
	 * @param array $options Element options array possibly containing a custom setting.
	 * @param string $default Default value to use if no option has been set.
	 * @return string|bool Returns selected value or false if: option is set false, or neither option or default has value.
	 */
	public function selectAttribute($attribute, $custom, $default = ''){
		if(isset($custom[$attribute]) && $custom[$attribute] !== false){
			return $custom[$attribute];
		}
		else if($default !== ''){
			return $default;
		}
		return false;
	}

	/**
	 * Checks if value in model equals given value. Can also test array values.
	 *
	 * @param mixed $value Value to test for.
	 * @param string $name Name in the model.
	 * @return bool Returns true if match, otherwise, returns false.
	 */
	public function checkModelValue($value, $name){
		if(is_array($this->model->{$name})){
			if(in_array($value, $this->model->{$name})) return true;
			return false;
		} else {
			if($value == $this->model->{$name}) return true;
			return false;
		}
	}
	/**
	 * Gets required class of given type.
	 *
	 * @param string $type Type of class to check.
	 * @return string Required class of of given type, empty if nothing is required or defaults are not in use.
	 */
	public function getDefaultClass($type){
		// If default classes are not in use.
		if(!$this->useDefaults){
			return '';
		}
		if(isset($this->elementDefaultClass[$type])){
			return $this->elementDefaultClass[$type];
		} else {
			return '';
		}
	}
}

?>