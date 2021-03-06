<?php

/**
 * Html class creates HTML elements with the help of ElementConstructor class. Methods are named after the HTML elements they create.
 * Methods single out as separate parameters minimum attribute data that is necessary for constructing the element, and optional
 * attributes are brought in on the options array.
 */
abstract class Html {
	private static array $closedElements = ['a', 'div', 'label', 'textarea', 'select', 'table', 'thead', 'tbody', 'tr', 'th', 'td', 'button', 'ul', 'li'];

	private static function enclosed($name){
		if(in_array($name, self::$closedElements)) return true;
		return false;
	}

	/**
	 * Builds an HTML element.
	 *
	 * @param string $name Name of the element.
	 * @param string $content Content wrapped by the element, if applicable. If there is nothing to wrap, set to empty string.
	 * @param array $attributes Attributes as key-value pairs to be set to the element.
	 * @return string Returns the constructed element.
	 */
	private static function build($name, $content = '', $attributes = []){
		$element = '<' . $name;
		foreach($attributes as $key => $value){
			$element .= ' ' . $key . '="' . $value . '"';
		}
		$element .= '>' . $content;
		if(self::enclosed($name)) $element .= '</' . $name . '>';
		return $element;
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
	 * Creates a element.
	 */
	public static function a($href, $params = [], $text, $options = []){
		$tail = '';
		if(!empty($params)){
			foreach($params as $key => $value){
				if($tail === '') $tail .= '?';
				else $tail .= '&';
				$tail .= $key . '=' . $value;
			}
			$href .= $tail;
		}
		$options['href'] = $href;
		return self::build('a', $text, $options);
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
		return self::build('input', '', $options);
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
		return self::build('label', $text, $options);
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
		return self::build('textarea', $content, $options);
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
		return self::build('input', '', $options);
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
		return self::build('input', '', $options);
	}

	/**
	 * Creates a select element.
	 *
	 * @param array $content Array describing option elements. Uses following keys:
	 * - text: a string containing option's displayable text.
	 * - options: Array of attributes for the option element as key-value pairs.
	 * @param string $name Name attribute of element.
	 * @param array $options Array of attributes for the element as key-value pairs.
	 * @return string The element formatted in HTML.
	 */
	public static function select($content = [], $name, $options = []){
		$options['name'] = $name;
		// Create select's option elements.
		$subElement = '';
		foreach($content as $thisOption){
			$subElement .= self::build('option', $thisOption['text'], $thisOption['options']);
		}
		return self::build('select', $subElement, $options);
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
		return self::build('input', '', $options);
	}

	/**
	 * Creates a file input element.
	 *
	 * @param string $name Name attribute of element.
	 * @param array $options Array of attributes for the element as key-value pairs.
	 */
	public static function file($name, $options = []){
		$options['type'] = 'file';
		$options['name'] = $name;
		return self::build('input', '', $options);
	}

	/**
	 * Creates a button element.
	 *
	 * @param string $type Type attribute of element.
	 * @param string $text Button's displayable text.
	 * @param array $options Array of attributes for the element as key-value pairs.
	 */
	public static function button($type, $text, $options = []){
		$options['type'] = $type;
		return self::build('button', $text, $options);
	}

	/**
	 * Creates a div element.
	 *
	 * @param string $content Div content.
	 * @param array $options Array of attributes for the element as key-value pairs.
	 * @return string The element formatted in HTML.
	 */
	public static function div($content, $options = []){
		return self::build('div', $content, $options);
	}

	/**
	 * Creates table element.
	 */
	public static function table($content, $options = []){
		return self::build('table', $content, $options);
	}

	/**
	 * Creates a thead element.
	 */
	public static function thead($content, $options = []){
		return self::build('thead', $content, $options);
	}

	/**
	 * Creates a tbody element.
	 */
	public static function tbody($content, $options = []){
		return self::build('tbody', $content, $options);
	}

	/**
	 * Creates a table row element.
	 */
	public static function tr($content, $options = []){
		return self::build('tr', $content, $options);
	}

	/**
	 * Creates a table heading element.
	 */
	public static function th($content, $options = []){
		return self::build('th', $content, $options);
	}

	/**
	 * Creates a table data element.
	 */
	public static function td($content, $options = []){
		return self::build('td', $content, $options);
	}

	/**
	 * Creates img element.
	 */
	public static function img($src, $options = []){
		$options['src'] = $src;
		return self::build('img', '', $options);
	}

	/**
	 * Creates li elemenet.
	 */
	public static function li($content, $options = []){
		return self::build('li', $content, $options);
	}

	/**
	 * Creates ul element.
	 */
	public static function ul($content, $options = []){
		return self::build('ul', $content, $options);
	}
}

?>