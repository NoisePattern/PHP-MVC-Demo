<?php

class ElementConstructor {
	public $root;

	public function __construct(){
		$this->root = new DOMDocument('1.0', 'UTF-8');
	}

	/**
	 * Converts DOMDocument into HTML.
	 *
	 * @return string Returns content as HTML-formatted string.
	 */
	public function getHTML(){
		$html = $this->root->saveHTML();
		// Clear the document by creating an empty one.
		//$this->root = new DOMDocument('1.0', 'UTF-8');
		return $html;
	}

	/**
	 * Imports elements from another ElementConstructor. Used to combine page fragments into whole.
	 *
	 * @param object $other The imported ElementConstructor.
	 * @param string $target. Target element (div, p, etc) in the receiving ElementConstructor. Default is root.
	 */
	public function import($other, $target = false){
		$node = $this->root->importNode($other, true);
		// If no target specified, append to root.
		if($target === false){
			$this->append($node, $this->root);
		}
		// If target is specified as an element in DOMDocument, attach to element.
		else if(is_object($target)){
			$this->append($node, $target);
		}
		return $node;
	}

	/**
	 * Creates a new element. It must be appended to DOM.
	 *
	 * @param string $name The created element's name.
	 * @param array $attributes Attributes to be added to element as key-value pairs.
	 * @return object Returns the created element.
	 */
	public function createElement($name, $attributes = []){
		$element = $this->root->createElement($name);
		foreach($attributes as $key => $value){
			if($value === false || $value == '') continue;
				$this->setAttribute($element, $key, $value);
		}
		return $element;
	}

	/**
	 * Creates a new text node. It must be appended to an element, or DOM if no wrapping is wanted.
	 *
	 * @param string $text The text to put into the node.
	 * @return object Returns the created textnode.
	 */
	public function createText($text){
		return $this->root->createTextNode($text);
	}

	/**
	 * Sets innerHTML text to an element. Shortcut to using createText + append.
	 *
	 * @param string $text The text to set inside an element.
	 * @param object $element The target element.
	 */
	public function setText($text, $element){
		$textNode = $this->root->createTextNode($text);
		$this->append($textNode, $element);
//		$element->appendChild($textNode);
	}

	/**
	 * Sets attribute to element.
	 *
	 * @param object $element The target element.
	 * @param string $name Name of the attribute.
	 * @param string $value Value of the attribute.
	 */
	public function setAttribute($element, $name, $value){
		$element->setAttribute($name, $value);
	}

	/**
	 * Adds to element attribute's content.
	 *
	 * @param object $element The target element.
	 * @param string $name Name of the attribute.
	 * @param string $value Value to append to attribute content. Will be separated with space from existing content.
	 */
	public function addToAttribute($element, $name, $value){
		$oldValue = $element->getAttribute($name);
		if($oldValue != '') $oldValue .= ' ';
		$oldValue .= $value;
		$this->setAttribute($element, $name, $oldValue);
	}

	/**
	 * Gets element's attribute value.
	 *
	 * @param string $name The name of the attribute.
	 * @param object $element The target element.
	 * @param string Returns value of the attribute, or an empty string if attribute was not found.
	 */
	public function getAttribute($element, $name){
		return $element->getAttribute($name);
	}

	/**
	 * Removes element's attribute.
	 *
	 * @param object $element The target element.
	 * @param string $name The name of the attribute.
	 */
	function removeAttribute($element, $name){
		$element->removeAttribute($name);
	}

	/**
	 * Gets element's parent.
	 *
	 * @param object $element The target element.
	 * @return object Returns the parent element.
	 */
	public function getParent($element){
		return $element->parentNode;
	}

	/**
	 * Appends an element as last child of another element.
	 *
	 * @param object $child The element to be appended.
	 * @param object $parent The element that appends the other element as last child. If not given, target is root.
	 */
	public function append($child, $parent = false){
		if($parent === false){
			$this->root->appendChild($child);
		} else {
			$parent->appendChild($child);
		}
	}

	/**
	 * Inserts an element before another element. Method is called by the target's parent element.
	 *
	 * @param object $parent The element that is parent to the target, which is DOMDocument for top-level elements.
	 * @param object $target The element before which the insertion is done.
	 * @param object $insert The element to be inserted.
	 */
	public function insert($parent, $target, $insert){
		$parent->insertBefore($insert, $target);
	}
}

?>