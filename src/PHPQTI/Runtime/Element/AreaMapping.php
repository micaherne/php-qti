<?php

namespace PHPQTI\Runtime\Element;

class AreaMapping extends Element {
	
	public function __invoke($controller) {
	    $mapping = new \PHPQTI\Runtime\Processing\AreaMapping();
        foreach($this->children as $child) { // only mapEntry elements allowed
            $attrs = $child->__invoke($controller); // just the attributes array
            $entry = new \PHPQTI\Runtime\Processing\AreaMapEntry();
            $entry->shape = $attrs['shape'];
            $entry->coords = $attrs['coords'];
            $entry->mappedValue = $attrs['mappedValue'];
            $mapping->areaMapEntries[] = $entry;
        }
        if (isset($this->attrs['lowerBound'])) {
            $mapping->lowerBound = $this->attrs['lowerBound'];
        }
        if (isset($this->attrs['upperBound'])) {
            $mapping->upperBound = $this->attrs['upperBound'];
        }
        if (isset($this->attrs['defaultValue'])) {
            $mapping->defaultValue = $this->attrs['defaultValue'];
        }
        return $mapping;
	}
	
}