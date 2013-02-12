<?php

namespace PHPQTI\Runtime\Element;

class Value extends Element {

	public function __invoke($controller) {
		$result = '';
		foreach($this->children as $child) { // only text elements allowed
			$result .= $child->__invoke($controller);
		}
		return $result;
	}

}