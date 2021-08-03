<?php

class FormElement {
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
	public $elements = [];						// Storage for constructed form elements.
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
		// If wrapper doesn't exist.
		if(!array_key_exists('wrap', $this->elements)) $this->wrap();
		else if($this->elements['wrap'] === false){
			return $this->elements['control']->root->saveHTML();
		}
		// If label doesn't exist.
		if(!array_key_exists('label', $this->elements)) $this->label();
		$wrapper = $this->elements['wrap'];
		$this->elements['wrap'] = false;
		foreach($this->elementOrder as $order){
			if(isset($this->elements[$order]) && $this->elements[$order] !== false){
				$wrapper->import($this->elements[$order], 'div');
			}
		}
		return $wrapper->root->saveHTML();
	}

	/**
	 * Combines elements into a single unit, putting label, control and error elements inside the wrapper element, and returns
	 * the result as ElementConstructor object, which contains the elements as a DOMDocument. This allows for injectint the elements
	 * in any spot on other ElementConstructor objects, which wouldn't be possible if results were passed around as HTML strings.
	 * Note: the method does not return $this, so __toString cannot be invoked, and the method must be called last in method chain.
	 *
	 * @return object Returns an ElementConstructor object.
	 */
	public function getDOM(){
		$new = false;
		// If wrapper doesn't exist.
		if(!array_key_exists('wrap', $this->elements)){
			$this->wrap();
			$wrapper = $this->elements['wrap'];
		}
		// If content is not to be wrapped, create empty container for it.
		else if($this->elements['wrap'] === false){
			$new = true;
			$wrapper = new ElementConstructor();
		}
		// Get wrapper.
		else {
			$wrapper = $this->elements['wrap'];
		}
		// If label doesn't exist.
		if(!array_key_exists('label', $this->elements)) $this->label();
		foreach($this->elementOrder as $order){
			if($order !== 'wrap' && isset($this->elements[$order]) && $this->elements[$order] !== false){
				if(!$new){
					$wrapper->import($this->elements[$order], 'div');
				}
				else {
					$wrapper->import($this->elements[$order]);
				}
			}
		}
		return $wrapper;
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
		// Create element base and append to document.
		$ECon = new ElementConstructor();
		$element = $ECon->createElement('input');
		$ECon->append($element);
		// Set attributes.
		$ECon->setAttributes($element, ['type' => $type]);
		$ECon->setAttributes($element, ['name' => $this->name]);
		$ECon->setAttributes($element, ['id'=> $this->selectAttribute('id', $options, $this->name)]);
		$ECon->setAttributes($element, ['class' => $this->selectAttribute('class', $options, $this->getDefaultClass('input'))]);
		$ECon->setAttributes($element, ['value' => $this->selectAttribute('value', $options, $this->model->{$this->name})]);
		$ECon->setAttributes($element, ['placeholder' => $this->selectAttribute('placeholder', $options, $this->displayName)]);
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			$class = $this->getDefaultClass('error');
			if(!empty($class)) $ECon->setAttributes($element, ['class' => $ECon->getAttribute($element, 'class') . ' ' .$class]);
		}
		// Store constructed control.
		$this->elements['control'] = $ECon;
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
		// Create element base and append to document.
		$ECon = new ElementConstructor();
		$element = $ECon->createElement('label');
		$ECon->append($element);
		// Set attributes.
		$ECon->setAttributes($element, ['for'=> $this->selectAttribute('for', $options, $this->name)]);
		$ECon->setAttributes($element, ['class' => $this->selectAttribute('class', $options, $this->getDefaultClass('label'))]);
		// Attach text.
		$innerHTML = $ECon->createText(!empty($text) ? $text : $this->displayName);
		$ECon->append($innerHTML, $element);
		// Store constructed label.
		$this->elements['label'] = $ECon;
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
		$ECon = new ElementConstructor();
		$element = $ECon->createElement('input');
		$ECon->append($element);
		// Set attributes.
		$ECon->setAttributes($element, ['type' =>'hidden']);
		$ECon->setAttributes($element, ['id'=> $this->selectAttribute('id', $options, $this->name)]);
		$ECon->setAttributes($element, ['name' => $this->name]);
		$ECon->setAttributes($element, ['value' => $this->selectAttribute('value', $options, $this->model->{$this->name})]);
		// Store constructed control.
		$this->elements['control'] = $ECon;
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
		// Create element base and append to document.
		$ECon = new ElementConstructor();
		$element = $ECon->createElement('textarea');
		$ECon->append($element);
		// Set attributes.
		$ECon->setAttributes($element, ['name' => $this->name]);
		$ECon->setAttributes($element, ['id'=> $this->selectAttribute('id', $options, $this->name)]);
		$ECon->setAttributes($element, ['class' => $this->selectAttribute('class', $options, $this->getDefaultClass('textarea'))]);
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			$class = $this->getDefaultClass('error');
			if(!empty($class)) $ECon->setAttributes($element, ['class' => $ECon->getAttribute($element, 'class') . ' ' .$class]);
		}
		// Set text content.
		$text = $ECon->createText(html_entity_decode($this->model->{$this->name}));
		$ECon->append($text, $element);
		// Store constructed control.
		$this->elements['control'] = $ECon;
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
		// Create element base and append to document.
		$ECon = new ElementConstructor();
		$element = $ECon->createElement('select');
		$ECon->append($element);
		// Set attributes.
		$ECon->setAttributes($element, ['name' => $this->name]);
		$ECon->setAttributes($element, ['id'=> $this->selectAttribute('id', $options, $this->name)]);
		$ECon->setAttributes($element, ['class' => $this->selectAttribute('class', $options, $this->getDefaultClass('select'))]);
		if(isset($options['multiple'])) $ECon->setAttributes($element, ['multiple' => 'multiple']);
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			$class = $this->getDefaultClass('error');
			if(!empty($class)) $ECon->setAttributes($element, ['class' => $ECon->getAttribute($element, 'class') . ' ' .$class]);
		}
		// Create options.
		foreach($content as $key => $value){
			$option = $ECon->createElement('option');
			$ECon->setAttributes($option, ['value' => $key]);
			$ECon->setText($value, $option);
			if(is_array($this->model->{$this->name})){
				if(in_array($key, $this->{$this->name})) $ECon->setAttributes($option, ['selected' => 'selected']);
			} else {
				if($this->model->{$this->name} == $key) $ECon->setAttributes($option, ['selected' => 'selected']);
			}
			$ECon->append($option, $element);
		}
		// Store constructed control.
		$this->elements['control'] = $ECon;
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
		// Create element base and append to document.
		$ECon = new ElementConstructor();
		$element = $ECon->createElement('input');
		$ECon->append($element);
		// Set attributes.
		$value = $this->selectAttribute('value', $options, '1');
		$ECon->setAttributes($element, ['name' =>isset($options['grouped']) ? $this->name . '[]' : $this->name]);
		$ECon->setAttributes($element, ['type' => 'checkbox']);
		$ECon->setAttributes($element, ['value' => $value]);
		$ECon->setAttributes($element, ['id'=> $this->selectAttribute('id', $options, $this->name)]);
		$ECon->setAttributes($element, ['class' => $this->selectAttribute('class', $options, $this->getDefaultClass('checkbox'))]);
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			$class = $this->getDefaultClass('error');
			if(!empty($class)) $ECon->setAttributes($element, ['class' => $ECon->getAttribute($element, 'class') . ' ' .$class]);
		}
		// Checked state.
		if(is_array($this->model->{$this->name})){
			if(in_array($value, $this->model->{$this->name})) $ECon->setAttributes($element, ['checked' => 'checked']);
		} else {
			if($this->model->{$this->name} == $value) $ECon->setAttributes($element, ['checked' => 'checked']);
		}
		// Create unchecked hidden value's element.
		if(isset($options['unchecked'])){
			$form = new Form();
			$hidden = $form->using($this->model, $this->name)->hidden(['value' => $options['unchecked']])->getDOM();
			$ECon->import($hidden);
		}
		// Store constructed control.
		$this->elements['control'] = $ECon;
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
		$ECon = new ElementConstructor();
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
			$ECon->import($checkbox);
		}
		// Store constructed control.
		$this->elements['control'] = $ECon;
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
		$ECon = new ElementConstructor();
		$this->elements['label'] = false;
		// Loop through radio content.
		foreach($list as $key => $value){
			$div = $ECon->createElement('div');
			$ECon->setAttributes($div, ['class' => $this->selectAttribute('wrapClass', $options, $this->getDefaultClass('checkDiv'))]);
			$ECon->append($div);
			$element = $ECon->createElement('input');
			$ECon->append($element, $div);
			// Set attributes.
			$id = $this->name . '_' . $key;
			$ECon->setAttributes($element, ['name' => $this->name]);
			$ECon->setAttributes($element, ['type' => 'radio']);
			$ECon->setAttributes($element, ['value' => $key]);
			$ECon->setAttributes($element, ['id'=> $id]);
			$ECon->setAttributes($element, ['class' => $this->selectAttribute('class', $options, $this->getDefaultClass('radio'))]);
			// On error, add error class if error classes are enabled.
			if($this->hasError && $this->errorClasses){
				$class = $this->getDefaultClass('error');
				if(!empty($class)) $ECon->setAttributes($element, ['class' => $ECon->getAttribute($element, 'class') . ' ' .$class]);
			}
			$form = new Form();
			$label = $form->using($this->model, $this->name, ['noErrorMessage' => true])
				->label($value, ['for' => $id, 'class' => $this->selectAttribute('labelClass', $options, $this->getDefaultClass('checkLabel'))])
				->wrap(['noWrap' => true])->getDOM();
			$ECon->import($label, $div);
			if($key == array_key_last($list)) $ECon->import($this->elements['error'], $div);
		}
		$this->elements['control'] = $ECon;
		// Return object to method chain.
		return $this;
	}

	public function wrap($options = []){
		if(isset($options['noWrap'])){
			$this->elements['wrap'] = false;
			return $this;
		}
		// Create element base and append to document.
		$ECon = new ElementConstructor();
		$element = $ECon->createElement('div');
		$ECon->append($element);
		// Set attributes.
		$ECon->setAttributes($element, ['class' => $this->selectAttribute('class', $options, '')]);
		$this->elements['wrap'] = $ECon;
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
		// Create element base and append to document.
		$ECon = new ElementConstructor();
		$element = $ECon->createElement('input');
		$ECon->append($element);
		// Set attributes.
		$ECon->setAttributes($element, ['type' => 'range']);
		$ECon->setAttributes($element, ['name' => $this->name]);
		$ECon->setAttributes($element, ['id'=> $this->selectAttribute('id', $options, $this->name)]);
		$ECon->setAttributes($element, ['class' => $this->selectAttribute('class', $options, $this->getDefaultClass('range'))]);
		$ECon->setAttributes($element, ['value' => $this->selectAttribute('value', $options, $this->model->{$this->name})]);
		$ECon->setAttributes($element, ['min' => $this->selectAttribute('min', $options)]);
		$ECon->setAttributes($element, ['max' => $this->selectAttribute('max', $options)]);
		$ECon->setAttributes($element, ['step' => $this->selectAttribute('step', $options)]);
		// On error, add error class if error classes are enabled.
		if($this->hasError && $this->errorClasses){
			$class = $this->getDefaultClass('error');
			if(!empty($class)) $ECon->setAttributes($element, ['class' => $ECon->getAttribute($element, 'class') . ' ' .$class]);
		}
		// Store constructed control.
		$this->elements['control'] = $ECon;
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
		$ECon = new ElementConstructor();
		$div = $ECon->createElement('div');
		$ECon->append($div);
		$ECon->setAttributes($div, ['class' => $this->getDefaultClass('errorDiv')]);
		$ECon->setText($error, $div);
		$this->elements['error'] = $ECon;
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