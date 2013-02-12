<?php

namespace PHPQTI\Runtime\Element;

use PHPQTI\Runtime\Processing\Variable;

// TODO: Implement attributes and lookupTable
class OutcomeDeclaration extends Element {
	
	public function __invoke($controller) {
		$result = new Variable($this->attrs['cardinality'], $this->attrs['baseType']);
		foreach($this->children as $child) {
			if($child instanceof DefaultValue) {
					$result->defaultValue = $child($controller);
			}
		}
		$controller->outcome[$this->attrs['identifier']] = $result;
	}
	
}