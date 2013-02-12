<?php

namespace PHPQTI\Runtime\Element;

use PHPQTI\Runtime\Processing\Variable;

class ResponseDeclaration extends Element {
	
	public function __invoke($controller) {
		$result = new Variable($this->attrs['cardinality'], $this->attrs['baseType']);
		foreach($this->children as $child) {
			if($child instanceof CorrectResponse) {
					$result->correctResponse = $child($controller);
			} else if ($child instanceof Mapping) {
			    $result->mapping = $child($controller);
			} else if ($child instanceof AreaMapping) {
			    $result->areaMapping = $child($controller);
			}
		}
		$controller->response[$this->attrs['identifier']] = $result;
	}
	
}