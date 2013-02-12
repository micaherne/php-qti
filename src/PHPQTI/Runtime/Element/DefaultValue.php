<?php

namespace PHPQTI\Runtime\Element;

class DefaultValue extends Element {
	
	public function __invoke($controller) {
		$values = array();
        foreach($this->children as $child) { // only value elements allowed
            $values[] = $child->__invoke($controller);
        }
        return $values;
	}
	
}