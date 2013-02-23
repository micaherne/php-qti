<?php

namespace PHPQTI\Runtime\Element;

use PHPQTI\Runtime\Processing\Variable;

abstract class VariableDeclaration extends Element {
	
	public function __invoke($controller) {
		$result = new Variable($this->attrs['cardinality'], $this->attrs['baseType']);
	    if (isset($this->attrs['paramVariable']) && $this->attrs['paramVariable'] == 'true') {
		    $result->paramVariable = true;
		}
	    if (isset($this->attrs['mathVariable']) && $this->attrs['mathVariable'] == 'true') {
		    $result->mathVariable = true;
		}
		foreach($this->children as $child) {
			if($child instanceof CorrectResponse) {
			    $correctResponseValues = $child($controller); // 5.3 compatibility
				// Value is an array only if cardinality is not single
				if($this->attrs['cardinality'] == 'single') {
					$result->correctResponse = $correctResponseValues[0];
				} else {
					$result->correctResponse = $correctResponseValues;
				}
				// interpretation attribute
				if(isset($child->attrs['interpretation'])) {
				    $result->correctResponseInterpretation = $child->attrs['interpretation'];
				}
			} else if ($child instanceof DefaultValue) {
			    $defaultValueValues = $child($controller); // 5.3 compatibility
				// defaultValue is an array only if cardinality is not single
				if($this->attrs['cardinality'] == 'single') {
					$result->defaultValue = $defaultValueValues[0];
				} else {
					$result->defaultValue = $defaultValueValues;
				}
				// interpretation attribute
				if(isset($child->attrs['interpretation'])) {
				    $result->defaultValueInterpretation = $child->attrs['interpretation'];
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