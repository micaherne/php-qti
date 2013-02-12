<?php

namespace PHPQTI\Runtime\Element;

use PHPQTI\Runtime\Processing\Variable;

class TemplateDeclaration extends VariableDeclaration {
	
	public function setVariable($controller, $result) {
		$controller->template[$this->attrs['identifier']] = $result;
	}
	
}