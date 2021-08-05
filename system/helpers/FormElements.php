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
	public $elements = [
		'wrap' => null,
		'label' => null,
		'control' => null,
		'error' => null
	];						// Storage for constructed form elements.
	public $labelClass = '';
	public $elementClass = '';
	public $wrapClass = '';
	public $hidden = false;						// Creating a hidden element. This omits other output except the element itself.
	public $elementDefaultClass = [				// Class attribute values required by by various HTML elements, as mandated by Bootstrap v5.
		'input' => 'form-control',				// Class required by input element of type text, email or password.
		'label' => 'form-label',				// Class required by label element.
		'checkLabel' => 'form-check-label',		// Class required be label element next to a checkbox or radio.
		'textarea' => 'form-control',			// Class required by textarea element.
		'select' => 'form-select',				// Class required by select element.
		'checkbox' => 'form-check-input',		// Class required by checkbox element.
		'range' => 'form-range',				// Class required by range element.
		'radio' => 'form-check-input',			// Class required by radio element.
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
		$this->combineElements();
		return $this->DOM->getHTML();
	}

	/**
	 * Combines elements into a single unit, putting label, control and error elements inside the wrapper element, and returns
	 * the result as ElementConstructor object, which contains the elements as a DOMDocument. This allows for injectint the elements
	 * in any spot on other ElementConstructor objects, which wouldn't be possible if results were passed around as HTML strings.
	 * Note: the method does not return $this, so __toString cannot be invoked, and the method must be called last in method chain.
	 *
	 * @return object Returns an ElementConstructor object.
	 */
	public function getDOM($target = ''){
		if($target !== ''){
			return $this->elements[$target];
		}
		return $this->combineElements();
	}

	/**
	 * Combines elements into a single unit.
	 */
	public function combineElements(){
		$parent = $this->DOM->root;
		// If wrapper has been set to be omitted, skip.
		if($this->elements['wrap'] !== false){
			// If wrapper has not yet been created, create a default format wrapper.
			if(empty($this->elements['wrap'])){
				$this->wrap();
			}
			// Set as parent to attach elements to.
			$parent = $this->DOM->import($this->elements['wrap']);
		}
		// If label has been set to be omitted, skip.
		if($this->elements['label'] !== false){
			// If label has not yet been created, create a default format wrapper.
			if(empty($this->elements['label'])){
				$this->label();
			}
		}
		// If error has not been generated, omit error.
		if($this->elements['error'] !== false){
			if(empty($this->elements['error'])){
				$this->elements['error'] = false;
			}
		}
		// Loop through elements in creation order and combine.
		foreach($this->elementOrder as $order){
			if($this->elements[$order] !== false){
				if($order !== 'wrap'){
					if(is_array($this->elements[$order])){
						foreach($this->elements[$order] as $item){
							$this->DOM->import($item, $parent);
						}
					} else {
						$this->DOM->import($this->elements[$order], $parent);
					}
				}
			}
		}
		return $parent;
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
		// Create element and append to document.
		$element = $this->DOM->createElement('input', [
			'type' => $type,
			'name' => $this->name,
			'id'=> $this->selectAttribute('id', $options, $this->name),
			'class' => $this->selectAttribute('class', $options, $this->getDefaultClass('input')),
			'value' => $this->selectAttribute('value', $options, $this->model->{$this->name}),
			'placeholder' => $this->selectAttribute('placeholder', $options, $this->displayName)
		]);
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			$class = $this->getDefaultClass('error');
			if(!empty($class)) $this->DOM->addToAttribute($element, 'class', $class);
		}
		// Store constructed control.
		$this->elements['control'] = $element;
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
		// Create element and append to document.
		$element = $this->DOM->createElement('label', [
			'for'=> $this->selectAttribute('for', $options, $this->name),
			'class' => $this->selectAttribute('class', $options, $this->getDefaultClass('label'))
		]);
		// Attach text.
		$this->DOM->setText(!empty($text) ? $text : $this->displayName, $element);
		// Store constructed label.
		$this->elements['label'] = $element;
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
		// Create element base and append to document.
		$element = $this->DOM->createElement('input', [
			'type' =>'hidden',
			'id'=> $this->selectAttribute('id', $options, $this->name),
			'name' => $this->name,
			'value' => $this->selectAttribute('value', $options, $this->model->{$this->name})
		]);
		// Store constructed control.
		$this->elements['control'] = $element;
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
		// Create element and append to document.
		$element = $this->DOM->createElement('textarea', [
			'name' => $this->name,
			'id'=> $this->selectAttribute('id', $options, $this->name),
			'class' => $this->selectAttribute('class', $options, $this->getDefaultClass('textarea'))
		]);
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			$class = $this->getDefaultClass('error');
			if(!empty($class)) $this->DOM->addToAttribute($element, 'class' , $class);
		}
		// Set text content.
		$this->DOM->setText(html_entity_decode($this->model->{$this->name}), $element);
		// Store constructed control.
		$this->elements['control'] = $element;
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates a dropdown element. The model contains the selected value, either as a single value or as an array, while the method receives a key-value list of dropdown content.
	 *
	 * @param array $content An associative array of key-value pairs populate dropdown options.
	 * @param array $options Array of options as key-value pairs to format the element. Supported keys:
	 * - id: Sets custom id attribute to element. Default value is name from model. If set, must also be set as label's for-attribute.
	 * - class: Sets custom class value to element. Default value is 'form-select' if defaults are in use, none otherwise.
	 * - multiple: When set to true, dropdown is made multiselect.
	 * @return object Returns object back to method chain.
	 */
	public function dropdown($content, $options = []){
		// Create element and append to document.
		$element = $this->DOM->createElement('select', [
			'name' => $this->name,
			'id'=> $this->selectAttribute('id', $options, $this->name),
			'class' => $this->selectAttribute('class', $options, $this->getDefaultClass('select'))
		]);
		if(isset($options['multiple'])) $this->DOM->setAttribute($element, 'multiple', 'multiple');
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			$class = $this->getDefaultClass('error');
			if(!empty($class)) $this->DOM->addToAttribute($element, 'class', $class);
		}
		// Create options.
		foreach($content as $key => $value){
			$option = $this->DOM->createElement('option', ['value' => $key]);
			$this->DOM->setText($value, $option);
			if(is_array($this->model->{$this->name})){
				if(in_array($key, $this->{$this->name})) $this->DOM->setAttribute($option, 'selected', 'selected');
			} else {
				if($this->model->{$this->name} == $key) $this->DOM->setAttribute($option, 'selected', 'selected');
			}
			$this->DOM->append($option, $element);
		}
		// Store constructed control.
		$this->elements['control'] = $element;
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
		// Create element and append to document.
		$value = $this->selectAttribute('value', $options, '1');
		$element = $this->DOM->createElement('input', [
			'name' =>isset($options['grouped']) ? $this->name . '[]' : $this->name,
			'type' => 'checkbox',
			'value' => $value,
			'id'=> $this->selectAttribute('id', $options, $this->name),
			'class' => $this->selectAttribute('class', $options, $this->getDefaultClass('checkbox'))
		]);
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			$class = $this->getDefaultClass('error');
			if(!empty($class)) $this->DOM->addToAttribute($element, 'class', $class);
		}
		// Checked state.
		if(is_array($this->model->{$this->name})){
			if(in_array($value, $this->model->{$this->name})) $this->DOM->setAttribute($element, 'checked', 'checked');
		} else {
			if($this->model->{$this->name} == $value) $this->DOM->setAttribute($element,'checked', 'checked');
		}
		// Create unchecked hidden value's element.
		if(isset($options['unchecked'])){
			$form = new Form();
			$hidden = $form->using($this->model, $this->name)->hidden(['value' => $options['unchecked']])->getDOM('control');
			$this->DOM->import($hidden);
		}
		// Store constructed control.
		$this->elements['control'] = $element;
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
		// Create container for all checkboxes.
		$this->elements['label'] = false;
		// Loop through checkbox content.
		foreach($list as $key => $value){
			$form = new Form();
			$id = $this->name . '_' . $key;
			$options = ['elementOrder' => ['control','label','error']];
			// Suppress error divs on all but the last checkbox.
			if($key != array_key_last($list)) $options['noErrorMessage'] = true;
			$checkbox = $form->using($this->model, $this->name, $options)
				->checkbox(['id' => $id, 'value' => $key, 'grouped' => true, 'class' => $this->selectAttribute('class', $options, $this->getDefaultClass('checkbox'))])
				->label($value, ['for' => $id, 'class' => $this->selectAttribute('labelClass', $options, $this->getDefaultClass('checkLabel'))])
				->wrap(['class' => $this->selectAttribute('wrapClass', $options, $this->getDefaultClass('checkDiv'))])
				->getDOM();
			// Store constructed control.
			$this->elements['control'][] = $checkbox;
		}
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates radio buttons. If any of the keys in given list of radios matches the value in model, it is set selected.
	 *
	 * @param array $list Array from which array keys are used as radio values, and array values are used as radio label texts.
	 * @param array $options Array of options as key-value pairs to format the element. Supported keys:
	 * - class: Sets custom class value to element. Default value is 'form-check-input' if defaults are in use, none otherwise.
	 * - labelClass: Sets custom class value to label. Default is 'form-check-label' if defaults are in use, none otherwise.
	 * - wrapClass: Sets custom class to div that wraps radio and label. Default is 'form-check' if defaults are in use, none otherwise.
	 * @return object Returns object back to method chain.
	 */
	public function radio($list, $options = []){
		// Create container for all radios.
		$this->elements['label'] = false;
		// Loop through radio content.
		foreach($list as $key => $value){
			// Div container for radio and label.
			$div = $this->DOM->createElement('div',	['class' => $this->selectAttribute('wrapClass', $options, $this->getDefaultClass('checkDiv'))]);
			// Radio element.
			$id = $this->name . '_' . $key;
			$element = $this->DOM->createElement('input', [
				'name' => $this->name,
				'type' => 'radio',
				'value' => $key,
				'id'=> $id,
				'class' => $this->selectAttribute('class', $options, $this->getDefaultClass('radio'))
			]);
			// Selected state.
			if($this->model->{$this->name} == $key) $this->DOM->setAttribute($element, 'checked', 'checked');
			$this->DOM->append($element, $div);
			// On error, add error class if error classes are enabled.
			if($this->hasError && $this->errorClasses){
				$class = $this->getDefaultClass('error');
				if(!empty($class)) $this->DOM->addToAttribute($element, 'class', $class);
			}
			// Label element.
			$form = new Form();
			$label = $form->using($this->model, $this->name, ['noErrorMessage' => true])
				->label($value, ['for' => $id, 'class' => $this->selectAttribute('labelClass', $options, $this->getDefaultClass('checkLabel'))])
				->wrap(['noWrap' => true])->getDOM('label');
			$this->DOM->import($label, $div);
			if($key == array_key_last($list) && $this->hasError){
				$this->DOM->append($this->elements['error'], $div);
			}
			$this->elements['control'][] = $div;
		}
		$this->elements['error'] = false;
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
		// Create element base and append to document.
		$element = $this->DOM->createElement('div', [
			'class' => $this->selectAttribute('class', $options, '')
		]);
		$this->elements['wrap'] = $element;
		// Return object to method chain.
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
		// Create element and append to document.
		$element = $this->DOM->createElement('input', [
			'type' => 'range',
			'name' => $this->name,
			'id'=> $this->selectAttribute('id', $options, $this->name),
			'class' => $this->selectAttribute('class', $options, $this->getDefaultClass('range')),
			'value' => $this->selectAttribute('value', $options, $this->model->{$this->name}),
			'min' => $this->selectAttribute('min', $options),
			'max' => $this->selectAttribute('max', $options),
			'step' => $this->selectAttribute('step', $options)
		]);
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			$class = $this->getDefaultClass('error');
			if(!empty($class)) $this->DOM->addToAttribute($element, 'class', $class);
		}
		// Store constructed control.
		$this->elements['control'] = $element;
		// Return object to method chain.
		return $this;
	}

	/**
	 * Creates an error display div.
	 *
	 * @param string $error The error text to display.
	 */
	public function error($error){
		if($this->errorMessages === false) return;
		$div = $this->DOM->createElement('div', ['class' => $this->getDefaultClass('errorDiv')]);
		$this->DOM->setText($error, $div);
		$this->elements['error'] = $div;
	}

	/**
	 * Selects an attribute value from given options.
	 *
	 * @param string $attribute name of the attribute.
	 * @param array $options Element options array possibly containing a custom setting.
	 * @param string $default Default value to use if no option has been set.
	 * @return string|boolean Returns selected value or false if: option is set false, or neither option or default has value.
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