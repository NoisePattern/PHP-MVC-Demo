<?php

/**
 * HtmlHelper class provides common methods for HTML builder classes.
 */
abstract class HtmlHelper {
	/**
	 * Selects an attribute value from given options.
	 *
	 * @param string $attribute name of the attribute.
	 * @param array $custom Element options array possibly containing a custom setting.
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