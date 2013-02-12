<?php

namespace PHPQTI\Runtime\Processing;

/**
 * Represents a mapping of values.
 * 
 * @author Michael Aherne
 *
 */
class Mapping {
    
    public $lowerBound = null;
    public $upperBound = null;
    public $defaultValue = 0;
    public $entries = array();
    public $caseSensitive = array();
    
}