<?php

namespace PHPQTI\Runtime\Element;

use PHPQTI\Runtime\Processing\Variable;

// TODO: Implement attributes and lookupTable
class OutcomeDeclaration extends VariableDeclaration {
		
	protected function setVariable($controller, $result) {
		$controller->outcome[$this->attrs['identifier']] = $result;
	}
	
}