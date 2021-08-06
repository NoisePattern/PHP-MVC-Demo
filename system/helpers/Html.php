<?php

/**
 * Html class creates HTML elements with the help of ElementConstructor class. Methods are named after the HTML elements they create.
 * Methods single out as separate parameters minimum attribute data that is necessary for constructing the element, and optional
 * attributes are brought in on the options array.
 */
abstract class Html {
	public static ?ElementConstructor $EC = null;

	/**
	 * Instantiates and returns an ElementConstructor.
	 */
	private static function dom(){
		if(self::$EC == null){
			self::$EC = new ElementConstructor();
		}
		return self::$EC;
	}

	/**
	 * Element attribute creation helper. In given options array, if a class exists and has content, the value is appended to content,
	 * otherwise the class value is set to value. If value is empty, nothing happens.
	 *
	 * @param array $options Target array of operation.
	 * @param string $attribute Target attribute name in array.
	 * @param string $value Value to be appended or set into the target attribute.
	 */
	public static function addToAttribute(&$options, $attribute, $value){
		// Do nothing if value is not set.
		if($value == '') return;
		// If attribute is set and is not empty, append to its current value.
		if(isset($options[$attribute]) && $options[$attribute] !== ''){
			$options[$attribute] .= ' ' . $value;
		}
		// Otherwise set attribute value to given value.
		else {
			$options[$attribute] = $value;
		}
	}

	/**
	 * Element attribute creation helper. In given options array, if class exists, it retains its value, otherwise given value is set to it.
	 *
	 * @param array $options Target array of operation.
	 * @param array $attributes Target attributes and values as key-value pairs.
	 */
	public static function setAttribute(&$options, $attributes){
		foreach($attributes as $key => $value){
			if(isset($options[$key])) continue;
			if($value !== '') $options[$key] = $value;
		}
	}

	/**
	 * Element attribute creation helper. Selects an attribute between options entry or given value, depending which exists and is set.
	 *
	 * @param $attribute Name of the attribute.
	 * @param array $options Attributes array.
	 * @param string $value Attribute value.
	 */
	public static function selectAttribute($attribute, $options, $value){
		if(isset($options[$attribute]) && $options[$attribute] !== ''){
			return $options[$attribute];
		}
		else if($value !== ''){
			return $value;
		}
		return '';
	}

	/**
	 * Creates an input element.
	 *
	 * @param string $type Type attribute of element, one of:
	 * - text: a text input.
	 * - email: an email input.
	 * - password: a password input.
	 * - hidden: a hidden input.
	 * @param string $name Name attribute of element.
	 * @param string $value Value attribute of element.
	 * @param array $options Array of attributes for the element as key-value pairs.
	 * @return string The element formatted in HTML.
	 */
	public static function input($type, $name, $value, $options = []){
		$options['type'] = $type;
		$options['name'] = $name;
		$options['value'] = (string) $value;
		self::dom()->createElement('input', $options, '');
		return self::dom()->getHTML();
	}

	/**
	 * Creates a label element.
	 *
	 * @param string $text Text displayed on the label.
	 * @param string $for For attribute of element.
	 * @param array $options Array of attributes for the element as key-value pairs.
	 * @return string The element formatted in HTML.
	 */
	public static function label($text, $for, $options = []){
		$options['for'] = $for;
		$element = self::dom()->createElement('label', $options, '');
		self::dom()->setText($text, $element);
		return self::dom()->getHTML();
	}

	/**
	 * Creates a textarea element.
	 *
	 * @param string $content Textarea content.
	 * @param array $options Array of attributes for the element as key-value pairs.
	 * @return string The element formatted in HTML.
	 */
	public static function textarea($content = '', $name = '', $options = []){
		$options['name'] = $name;
		$element = self::dom()->createElement('textarea', $options, '');
		self::dom()->setText($content, $element);
		return self::dom()->getHTML();
	}

	/**
	 * Creates a checkbox element.
	 *
	 * @param string $name Name attribute of element.
	 * @param string $value Value attribute of element.
	 * @param array $options Array of attributes for the element as key-value pairs.
	 * @return string The element formatted in HTML.
	 */
	public static function checkbox($name, $value, $options = []){
		$options['type'] = 'checkbox';
		$options['name'] = $name;
		$options['value'] = (string) $value;
		self::dom()->createElement('input', $options, '');
		return self::dom()->getHTML();
	}

	/**
	 * Creates a radio element.
	 *
	 * @param string $name Name attribute of element.
	 * @param string $value Value attribute of element.
	 * @param array $options Array of attributes for the element as key-value pairs.
	 * @return string The element formatted in HTML.
	 */
	public static function radio($name, $value, $options = []){
		$options['type'] = 'radio';
		$options['name'] = $name;
		$options['value'] = (string) $value;
		self::dom()->createElement('input', $options, '');
		return self::dom()->getHTML();
	}

	/**
	 * Creates a select element.
	 *
	 * @param array $content Array of key-value pairs to describe values and displayed text of select's option elements.
	 * @param array|int $selected Value or values of keys in content array to be set as selected options.
	 * @param string $name Name attribute of element.
	 * @param array $options Array of attributes for the element as key-value pairs.
	 * @return string The element formatted in HTML.
	 */
	public static function select($content = [], $selected, $name, $options = []){
		$options['name'] = $name;
		$element = self::dom()->createElement('select', $options, '');
		// Create select's option elements.
		foreach($content as $value => $text){
			$subOptions = [];
			$subOptions['value'] = $value;
			if(is_array($selected)){
				if(in_array($value, $selected)) $subOptions['selected'] = 'selected';
			} else {
				if($value == $selected)  $subOptions['selected'] = 'selected';
			}
			$subElement = self::dom()->createElement('option', $subOptions);
			self::dom()->setText($text, $subElement);
			self::dom()->import($subElement, $element);
		}
		return self::dom()->getHTML();
	}

	/**
	 * Creates range element.
	 *
	 * @param string $name Name attribute of element.
	 * @param mixed $value Value attribute of element.
	 * @param array $options Array of attributes for the element as key-value pairs.
	 */
	public static function range($name, $value, $options = []){
		$options['type'] = 'range';
		$options['name'] = $name;
		$options['value'] = $value;
		self::dom()->createElement('input', $options, '');
		return self::dom()->getHTML();
	}

	/**
	 * Creates a button element.
	 *
	 * @param string $type Type attribute of element.
	 * @param string $text Button's displayable text.
	 * @param array $options Array of attributes for the element as key-value pairs.
	 */
	public static function button($type, $text, $options){
		$options['type'] = $type;
		$element = self::dom()->createElement('button', $options, '');
		self::dom()->setText($text, $element);
		return self::dom()->getHTML();
	}

	/**
	 * Creates a div element.
	 *
	 * @param string $text Div text content.
	 * @param string $html Div HTML content.
	 * @param array $options Array of attributes for the element as key-value pairs.
	 * @return string The element formatted in HTML.
	 */
	public static function div($text = '', $html = '', $options = []){
		$element = self::dom()->createElement('div', $options, '');
		if($text !== '') self::dom()->setText($text, $element);
		else if($html !== '') self::dom()->setHtml($html, $element);
		return self::dom()->getHTML();
	}

}

?>