<?php

namespace PHPQTI\Runtime\Element;

use PHPQTI\Runtime\Processing\Variable;

abstract class VariableDeclaration extends Element {
	
	public function __invoke($controller) {
		$result = new Variable($this->attrs['cardinality'], $this->attrs['baseType']);
		foreach($this->children as $child) {
			if($child instanceof CorrectResponse) {
				// Value is an array only if cardinality is not single
				if($this->attrs['cardinality'] == 'single') {
					$result->correctResponse = $child($controller)[0];
				} else {
					$result->correctResponse = $child($controller);
				}
			} else if ($child instanceof Mapping) {
			    $result->mapping = $child($controller);
			} else if ($child instanceof AreaMapping) {
			    $result->areaMapping = $child($controller);
			}
		}
		$this->setVariable($controller, $result);
	}
	
	abstract protected function setVariable($controller, $result);
	
}