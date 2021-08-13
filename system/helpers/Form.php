<?php

class Form extends HtmlHelper {
	/**
	 * Creates HTML to open a form.
	 *
	 * @param string $action Form's action.
	 * @param string $method Form's method.
	 * @param array @options Array of options as key-value pairs to format the element. Supported keys:
	 * - class: Sets class value.
	 * - enctype: Sets form enctype.
	 * - novalidate: If set, adds novalidate attribute.
	 * @return string Returns opening form element.
	 */
	public function openForm($action, $method, $options = []){
		// Novalidate attribute skips Bootstrap's pre-validation.
		$html = '<form action="' . $action . '" method="' . $method . '"';
		if(isset($options['id'])){
			$html .= ' id="' . $options['id'] . '"';
		}
		if(isset($options['class'])){
			$html .= ' class="' . $options['class'] . '"';
		}
		if(isset($options['novalidate'])){
			$html .= ' novalidate';
		}
		if(isset($options['enctype'])){
			$html .= ' enctyle="' . $options['enctype'] . '"';
		}
		$html .= '>';
		return $html;
	}

	/**
	 * Creates HTML to close a form.
	 *
	 * @return string Returns closing form element.
	 */
	public function closeForm(){
		return '</form>';
	}

	/**
	 * Begins constructing a new form elements group. Creates and returns a form elements object to start a method chain.
	 *
	 * @param object $model The model to use to construct elements.
	 * @param string $name The name of the model field to use to construct the elements.
	 * @param array $options FormElement options as key-value pairs. Valid keys are:
	 * - elementOrder: Array defining custom element order. Valid values in default order are: ['label', 'control', 'error'].
	 * - noDefault: If set, Bootstrap default values for classes are not used.
	 * - noErrorClass: If set, error classes are omitted from form elements. Bootstrap will also hide error message elements if form elements don't have error classes, so this
	 * essentially hides all errors unless custom CSS is poking into Bootstrap CSS.
	 * - noErrorMessage: If set, error message elements are omitted. This is useful when grouping elements and a common error message is inserted to non-standard position.
	 * @return object Returns FormElement object, the HTML result can be echoed due to __toString() magic method in the object.
	 */
	public function using($model = '', $name = '', $options = []) : FormElement {
		return new FormElement($model, $name, $options);
	}
}
?>