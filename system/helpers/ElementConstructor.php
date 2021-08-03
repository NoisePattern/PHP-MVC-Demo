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
		$nodes = $other->root->getElementsByTagName('*');
		foreach($nodes as $node){
			// Only import nodes that have root (#document) as their parent. This because getElementsByTagName('*') appears to duplicate elements
			// that are not direct children document root, when there are multiple children to document root.
			if($node->parentNode->nodeName !== '#document') continue;
			$nodes = $this->root->importNode($node, true);
			if($target === false){
				$this->append($nodes, $this->root);
			}
			else if(is_object($target)){
				$this->append($nodes, $target);
			}
			else {
				$list = $this->root->getElementsByTagName($target);
				$item = $list->item(0);
				$this->append($nodes, $item);
			}
		}
	}

	/**
	 * Creates a new element. It must be appended to DOM.
	 *
	 * @param string $name The created element's name.
	 * @return object Returns the created element.
	 */
	public function createElement($name){
		return $this->root->createElement($name);
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
		$element->appendChild($textNode);
	}

	/**
	 * Sets attributes to element.
	 *
	 * @param object $element The target element.
	 * @param array $attributes An array of attributes as key-value pairs. If value is false or an empty string, attribute is not set.
	 */
	public function setAttributes($element, $attributes){
		foreach($attributes as $key => $value){
			if($value === false || $value == '') continue;
				$element->setAttribute($key, $value);
		}
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