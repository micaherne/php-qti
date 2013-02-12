<?php

namespace PHPQTI\Runtime\Element;

class Mapping extends Element {
	
	public function __invoke($controller) {
	    $mapping = new \PHPQTI\Runtime\Processing\Mapping();
        foreach($this->children as $child) { // only mapEntry elements allowed
            $attrs = $child->__invoke($controller); // just the attributes array
            $mapping->entries[$attrs['mapKey']] = $attrs['mappedValue'];
            if (isset($attrs['caseSensitive']) && $attrs['caseSensitive']) {
                $mapping->caseSensitive[$attrs['mapKey']] = true;
            } else {
                $mapping->caseSensitive[$attrs['mapKey']] = false;
            }
        }
        if (isset($attrs['lowerBound'])) {
            $mapping->lowerBound = $attrs['lowerBound'];
        }
        if (isset($attrs['upperBound'])) {
            $mapping->upperBound = $attrs['upperBound'];
        }
        if (isset($attrs['defaultValue'])) {
            $mapping->defaultValue = $attrs['defaultValue'];
        }
        return $mapping;
	}
	
}