<?php

namespace PHPQTI\Runtime;
use PHPQTI\Model\CorrectResponse;
use PHPQTI\Model\DefaultValue;
use PHPQTI\Model\Mapping;

use PHPQTI\Runtime\Exception\ProcessingException;
use PHPQTI\Runtime\Exception\NotImplementedException;

class QTIVariable {

    public $cardinality;
    public $type;
    // For response vars, QTI has a candidateResponse wrapper for the value - any reason to implement?
    public $value;
    // For record variables, we need an empty array of fields
    public $field = array();
    public $correctResponse = null;
    public $correctResponseInterpretation = null;
    public $defaultValue = null;
    public $defaultValueInterpretation = null;
    public $paramVariable = false;
    public $mathVariable = false;
    public $mapping = null;

    /**
     * Create a qti variable
     * @param string $cardinality
     * @param string $type
     * @param array $params
     */
    public function __construct($cardinality, $type, $params = array()) {
        $this->cardinality = $cardinality;
        $this->type = $type; 

        $this->value = null;
        if (isset($params['value'])) {
            $this->value = $params['value'];
        }
        
        $this->correctResponse = null;
        if(isset($params['correctResponse'])) {
            $this->correctResponse = $params['correctResponse'];
        }

        $this->defaultValue = null;
        if(isset($params['defaultValue'])) {
            $this->defaultValue = $params['defaultValue'];
            $this->value = $this->defaultValue;
        }

        $this->mapping = null;
        if(isset($params['mapping'])) {
            $this->mapping = $params['mapping'];
        }

        $this->areaMapping = null;
        if (isset($params['areaMapping'])) {
            $this->areaMapping = $params['areaMapping'];
        }
        
    }

    public static function fromDeclaration($declaration) {
        $result = new QTIVariable($declaration->cardinality, $declaration->baseType);
        if (isset($declaration->paramVariable) && $declaration->paramVariable == 'true') {
            $result->paramVariable = true;
        }
        if (isset($declaration->mathVariable) && $declaration->mathVariable == 'true') {
            $result->mathVariable = true;
        }
        if (!is_null($declaration->getChildren())) {
            foreach($declaration->getChildren() as $child) {
                if($child instanceof CorrectResponse) {
                    $correctResponseValues = $child(null); // 5.3 syntax compatibility
                    // Value is an array only if cardinality is not single
                    if($declaration->cardinality == 'single') {
                        $result->correctResponse = $correctResponseValues[0];
                    } else {
                        $result->correctResponse = $correctResponseValues;
                    }
                    // interpretation attribute
                    if(isset($child->interpretation)) {
                        $result->correctResponseInterpretation = $child->interpretation;
                    }
                } else if ($child instanceof DefaultValue) {
                    $defaultValueValues = $child(null); // 5.3 syntax compatibility
                    
                    // defaultValue is an array only if cardinality is not single
                    if($declaration->cardinality == 'single') {
                        $result->defaultValue = $defaultValueValues[0];
                    } else {
                        $result->defaultValue = $defaultValueValues;
                    }
                    // interpretation attribute
                    if(isset($child->interpretation)) {
                        $result->defaultValueInterpretation = $child->interpretation;
                    }
            
                } else if ($child instanceof Mapping) {
                    $result->mapping = $child(null);
                } else if ($child instanceof AreaMapping) {
                    $result->areaMapping = $child(null);
                }
            }
        }
        return $result;
    }

    public function __toString(){
        return $this->cardinality . ' ' . $this->type . ' [' . $this->valueAsString() . ']';
    }

    public function valueAsString() {
        return (is_array($this->value) ? implode(',', $this->value) : $this->value);
    }

    // TODO: Implement caseSensitive
    public function mapResponse() {
        if (is_null($this->mapping)) {
        	throw new ProcessingException('Mapping required for mapResponse()');
        }
        $value = null;
        if ($this->cardinality == 'single') {
            foreach($this->mapping->getChildren() as $mapEntry) {
                if ($mapEntry->mapKey == $this->value) {
                    $value = $mapEntry->mappedValue;
                    break;
                }
            }
            if (is_null($value)) {
                $value = $this->mapping->defaultValue;
            }
        } else {
            // array_unique used because values should only be counted once - see mapResponse documentation
            $uniqueValues = array_unique($this->value);
            foreach($this->mapping->getChildren() as $mapEntry) {
                if (in_array($mapEntry->mapKey, $uniqueValues)) {
                    if (is_null($value)) {
                        $value = $mapEntry->mappedValue;
                    } else {
                        $value += $mapEntry->mappedValue;
                    }
                } else if ($this->type == 'pair') { // Pair can be either way round
                    $responseReversed = implode(' ', array_reverse(explode(' ', $mapEntry->mapKey)));
                    if (in_array($responseReversed, $uniqueValues)) {
                        if (is_null($value)) {
                            $value = $mapEntry->mappedValue;
                        } else {
                            $value += $mapEntry->mappedValue;
                        }
                    }
                }
            }
            
            if (is_null($value)) {
                $value = $this->mapping->defaultValue;
            } else if (!is_null($this->mapping->lowerBound) && $value < $this->mapping->lowerBound) {
                $value = $this->mapping->lowerBound;
            } else if (!is_null($this->mapping->upperBound) && $value > $this->mapping->upperBound) {
                $value = $this->mapping->upperBound;
            }
                
        }

        return new QTIVariable('single', 'float', array('value' => $value));
    }

    // TODO: Implement upper and lower bound
    // TODO: Check this algorithm - probably too simplistic given
    // possibilities for overlapping areas and priorities, also
    // multiple responses in same area
    // TODO: What do we do with defaultValue????
    public function mapResponsePoint() {
        if ($this->cardinality == 'single') {
            $values = array($this->value);
        } else {
            $values = $this->value;
        }

        $resultvalue = 0;
        foreach($this->areaMapping['areaMapEntry'] as $areaMapEntry) {

            // TODO: Inefficient - should pre-create array of testvars
            foreach($values as $value) {
                $testvar = new QTIVariable('single', 'point', array('value' => $value));
                if ($testvar->inside($areaMapEntry['shape'], $areaMapEntry['coords'])->value == true) {
                    $resultvalue += $areaMapEntry['mappedValue'];
                    continue 2; // ignore any other points in this area
                }
            }

        }

        if (isset($this->areaMapping['lowerBound'])) {
            if ($resultvalue < $this->areaMapping['lowerBound']) {
                $resultvalue = $this->areaMapping['lowerBound'];
            }
        }

        if (isset($this->areaMapping['upperBound'])) {
            if ($resultvalue > $this->areaMapping['upperBound']) {
                $resultvalue = $this->areaMapping['upperBound'];
            }
        }

        return new QTIVariable('single', 'float', array('value' => $resultvalue));
    }
    
    public static function mathConstant($name) {
        switch ($name) {
            case 'pi':
                return new QTIVariable('single', 'float', array('value' => pi()));
                break;
            case 'e':
                return new QTIVariable('single', 'float', array('value' => exp(1)));
                break;
            default:
                // TODO: Not defined in spec
                return;
        }
    }
    
    /**
     * Returns the result of a mathematical operation
     * 
     * The first parameter is the name of the operation, and any further
     * parameters are the variables to be acted on.
     * 
     * TODO: This is a very naive interpretation at the moment.
     * TODO: Need to check for NaN
     */
    public static function mathOperator($name, $params) {
        $result = new QTIVariable('single', 'float');
        foreach($params as $var) {
            if ($var->_isNull()) {
                return $result;
            }
        }
        
        switch($name) {
            case 'sin':
                $result->value = sin($params[0]->getValue());
                break;
            case 'cos':
                $result->value = cos($params[0]->getValue());
                break;
            case 'tan':
                $result->value = tan($params[0]->getValue());
                break;
            case 'sec':
            	throw new NotImplementedException('mathOperator:' . $name);
            case 'csc':
            	throw new NotImplementedException('mathOperator:' . $name);
            case 'cot':
            	throw new NotImplementedException('mathOperator:' . $name);
            case 'asin':
            	throw new NotImplementedException('mathOperator:' . $name);
            case 'acos':
            	throw new NotImplementedException('mathOperator:' . $name);
            case 'atan':
            	$result->value = atan($params[0]->getValue());
                break;
            case 'atan2':
            	$result->value = atan2($params[0]->getValue());
                break;
            case 'asec':
            	throw new NotImplementedException('mathOperator:' . $name);
            case 'acsc':
            	throw new NotImplementedException('mathOperator:' . $name);
            case 'acot':
            	throw new NotImplementedException('mathOperator:' . $name);
            case 'sinh':
            	$result->value = sinh($params[0]->getValue());
                break;
            case 'cosh':
            	$result->value = cosh($params[0]->getValue());
                break;
            case 'tanh':
            	$result->value = tanh($params[0]->getValue());
                break;
            case 'sech':
            	throw new NotImplementedException('mathOperator:' . $name);
            case 'csch':
            	throw new NotImplementedException('mathOperator:' . $name);
            case 'coth':
            	throw new NotImplementedException('mathOperator:' . $name);
            case 'log':
            	$result->value = log10($params[0]->getValue());
                break;
            case 'ln':
            	$result->value = log($params[0]->getValue());
                break;
            case 'exp':
                $result->value = exp($params[0]->getValue());
                break;
            case 'abs':
            	$result->value = abs($params[0]->getValue());
                break;
            case 'signum':
            	$v = $params[0]->getValue();
            	if ($v > 0) {
            		$result->value = 1;
            	} else if ($v < 0) {
            		$result->value = -1;
            	} else {
            		$result->value = 0;
            	}
                break;
            case 'floor':
            	$result->value = floor($params[0]->getValue());
                break;
            case 'ceil':
            	$result->value = ceil($params[0]->getValue());
                break;
            case 'toDegrees':
            	throw new NotImplementedException('mathOperator:' . $name);
            case 'toRadians':
            	throw new NotImplementedException('mathOperator:' . $name);
            default:
                throw new NotImplementedException('mathOperator:' . $name);
        }
        
        return $result;
    }
    
    /**
     * Returns the result of a mathematical operation
     *
     * The first parameter is the name of the operation, and any further
     * parameters are the variables to be acted on.
     *
     */
    public static function statsOperator($name, $param) {
        $result = new QTIVariable('single', 'float');
        if ($param->_isNull()) {
            return $result;
        }
        
        if (!is_array($param->value)) {
            return $result;
        }
        
        $mean = array_sum($param->value) / count($param->value);
        
        if ($name == 'mean') {
            $result->value = $mean;
        } else {
            $variance = 0;
            
            if (is_array($param->value)) {
                foreach($param->value as $val) {
                    $variance += (($val - $mean) * ($val - $mean));
                }
                $divisor = 1;
                if ($name == 'popSD' || $name == 'popVariance') {
                    $divisor = count($param->value);
                } else if ($name == 'sampleSD' || $name == 'sampleVariance') {
                    $divisor = count($param->value) - 1;
                } else {
                    throw new Exception ("Invalid statsOperator name $name");
                }
                
                $variance /= $divisor;
                
                if ($name == 'sampleVariance' || $name == 'popVariance') {
                    $result->value = $variance;
                } else if ($name == 'sampleSD' || $name == 'popSD') {
                    $result->value = sqrt($variance);
                } else {
                    throw new Exception ("Invalid statsOperator name $name");
                }
            } else {
                $result->value = 0; // no variance for a single value
            }
        }
    
        return $result;
    }
    
    public static function max() {
        $vars = func_get_args();
        // Allow a single array as well as a parameter list
        if (count($vars) == 1 && is_array($vars[0])) {
            $vars = $vars[0];
        }
        $result = new QTIVariable('single', 'float');
        $vals = array();
        $allIntegers = true;
        foreach($vars as $var) {
            if ($var->_isNull()) {
                return $result;
            }
            if ($var->type != 'integer') {
                $allIntegers = false;
            }
            if (is_array($var->value)) {
                $vals = array_merge($vals, $var->value);
            } else {
                $vals[] = $var->value;
            }
        }
        
        // Check for non-numeric - could be better implemented
        foreach($vals as $val) {
            if (!is_numeric($val)) {
                return $result;
            }
        }
        $result->value = max($vals);
        if ($allIntegers) {
            $result->type = 'integer';
        }
        
        return $result;
    }

    public static function min() {
        $vars = func_get_args();
        // Allow a single array as well as a parameter list
        if (count($vars) == 1 && is_array($vars[0])) {
            $vars = $vars[0];
        }
        $result = new QTIVariable('single', 'float');
        $vals = array();
        $allIntegers = true;
        foreach($vars as $var) {
            if (!is_object($var)) {
                print_r($var); die(" not an object\n");
            }
            if ($var->_isNull()) {
                return $result;
            }
            if ($var->type != 'integer') {
                $allIntegers = false;
            }
            if (is_array($var->value)) {
                $vals = array_merge($vals, $var->value);
            } else {
                $vals[] = $var->value;
            }
        }
        
        // Check for non-numeric - could be better implemented
        foreach($vals as $val) {
            if (!is_numeric($val)) {
                return $result;
            }
        }
        $result->value = min($vals);
        if ($allIntegers) {
            $result->type = 'integer';
        }
        
        return $result;
    }

    // TODO: This should be deprecated by the more specific methods
    // TODO: Make this work for things other than strings and arrays
    public static function compare($variable1, $variable2) {
        if (!is_array($variable1->value) && !(is_array($variable2->value))) {
            return strcmp($variable1->value, $variable2->value);
        }
        if (count($variable1->value) != count($variable2->value)) {
            // This doesn't mean anything
            return count($variable1->value) - count($variable2->value);
        }
        // If it's multiple just do a diff
        if ($variable1->cardinality == 'multiple') {
            return count(array_diff($variable1->value, $variable2->value));
        } else if ($variable1->cardinality == 'ordered') {
            // check them pairwise
            for($i = 0; $i < count($variable1->value); $i++) {
                if ($variable1->value[$i] != $variable2->value[$i]) {
                    // This doesn't mean too much either
                    return strcmp($variable1->value[$i], $variable2->value[$i]);
                }
            }
            return 0;
        }

        // default to not equal
        return -1;
    }

    /*
     * Response processing functions.
    */

    // TODO: Should we implement Built-in General Expressions here? At the moment
    // they're just implemented directly in Response_processing

    /*
     * 15.3 Operators
    */
    public static function gcd($params) {
    	$vals = array();
    	foreach($params as $param) {
    		if (is_array($param->value)) {
    			$vals = array_merge($param->value);
    		} else {
    			$vals[] = $param->value;
    		}
    	}
    	
    	$result = new QTIVariable('single', 'integer');
    	
    	if (count($vals) == 1) {
    		// do this to catch nulls
    		$result->value = QTIVariable::_getGCD(0, $vals[0]);
    		return $result;
    	}
    	// Now find the gcd of the $vals array
    	$gcd = QTIVariable::_getGCD($vals[0], $vals[1]);
    	if (is_null($gcd)) {
    		return $result;
    	}
    	
    	for ($i = 2; $i < count($vals); $i++) { 
    		$gcd = QTIVariable::_getGCD($gcd, $vals[$i]);
    		if (is_null($gcd)) {
    			return $result;
    		} 
    	}
    	
    	$result->value = $gcd;
    	return $result;
    }
    
    // Blagged from stackoverflow - not necessarily good
    private static function _getGCD($a, $b)
    {
    	if(is_null($a) || is_null($b) || !is_numeric($a) || !is_numeric($b)) {
    		return null;
    	}
    	
    	while ($b != 0)
    	{
    		$m = $a % $b;
    		$a = $b;
    		$b = $m;
    	}
    	return $a;
    }
    
    public static function lcm() {
    	$params = func_get_args();
    	$vals = array();
    	foreach($params as $param) {
    		if (is_array($param->getValue())) {
    			$vals = array_merge($param->getValue());
    		} else {
    			$vals[] = $param->getValue();
    		}
    	}
    	 
    	$result = new QTIVariable('single', 'integer');
    	 
    	if (count($vals) == 1) {
    		// do this to catch nulls
    		if (!is_null($vals[0])) {
    			$result->value = $vals[0];
    		}
    		return $result;
    	}
    	
    	if (in_array(null, $vals)) {
    		$result->value = null;
    		return $result;
    	}
    	
    	if (in_array(0, $vals)) {
    		$result->value = 0;
    		return $result;
    	}
    	 
    	$lcm = 0;
    	foreach($vals as $val) {
    		if(is_null($val)) {
    			return $result;
    		} else if ($lcm == 0) {
    			$lcm = $val;
    		} else {
    			$gcd = QTIVariable::_getGCD($val, $lcm);
    			$lcm = ($val / $gcd) * $lcm;
    		}
    	}
    	 
    	$result->value = $lcm;
    	return $result;
    }
    
    public static function multiple() {
        $params = func_get_args();

        // Null if no arguments passed
        if (count($params) == 0) {
            return new QTIVariable('multiple', 'identifier');
        } else {
            $result = new QTIVariable('multiple', 'identifier', array('value' => array()));
        }

        // Allow a single array as well as a parameter list
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }

        $allnull = true;
        foreach ($params as $param) {
            if (is_null($param->value)) {
                continue;
            } else {
                $allnull = false;
                $result->type = $param->type;
                if (is_array($param->value)) {
                    $result->value = array_merge($result->value, $param->value);
                } else {
                    $result->value[] = $param->value;
                }
            }
        }
        if ($allnull) {
            $result->value = null;
        }

        return $result;
    }

    /*
     * It looks from the documentation as if there is no difference
    * in the functionality of ordered and multiple, it is just the return
    * type that is different.
    */
    public static function ordered() {
        $params = func_get_args();
        $result = forward_static_call_array('\PHPQTI\Runtime\QTIVariable::multiple', $params);
        $result->cardinality = 'ordered';
        return $result;
    }

    public function containerSize() {
        $result = new QTIVariable('single', 'integer', array('value' => 0));
        if (is_null($this->value)){
            return $result;
        }
        if (is_array($this->value)) {
            $result->value = count($this->value);
        } else {
            $result->value = 1;
        }

        return $result;
    }

    // This is an internal isNull function that returns a PHP boolean, not a QTI one
    private function _isNull() {
        if (is_null($this->value)) {
            return true;
        }
        if (empty($this->value) && (in_array($this->type, array('multiple', 'ordered', 'string')))) {
            return true;
        }
        return false;
    }

    public function isNull() {
        $result = new QTIVariable('single', 'boolean', array('value' => $this->_isNull()));
        return $result;
    }

    public function index($i) {
        $result = new QTIVariable('single', $this->type);
        if (is_array($this->value) && $i <= count($this->value) && $i > 0) {
            $result->value = $this->value[$i - 1]; // 1 based indexing
        }
        return $result;
    }

    public function fieldValue($fieldidentifier) {
        throw new \Exception("Not implemented");
    }

    public function random() {
        $result = clone($this);
        $result->cardinality = 'single';
        if ($this->_isNull() || count($this->value) == 0) {
            $result->value = null;
        } else {
            $result->value = $this->value[mt_rand(0, count($this->value) - 1)];
        }
        return $result;
    }

    public function member($container) {
        $result = new QTIVariable('single', 'boolean');
        if (!$this->_isNull() && !$container->_isNull()) {
            $result->value = in_array($this->value, $container->value);
        }
        return $result;
    }

    public function delete($container) {
        $result = clone($container);
        if ($this->_isNull() || $container->_isNull()) {
            $result->value = null;
        } else {
            $thisvaluearray = is_array($this->value) ? $this->value : array($this->value);
            $result->value = array_diff($container->value, $thisvaluearray);
        }
        return $result;
    }

    public function contains($subsequence) {
        $result = new QTIVariable('single', 'boolean');
        if ($this->_isNull() || $subsequence->_isNull()) {
            $result->value = null;
            return $result;
        } else {
            $result->value = false;

            $testarr = is_array($subsequence->value) ? $subsequence->value : array($subsequence->value);
            $testcontainer = $this->value; // copy of array, not ref

            if ($this->cardinality == 'multiple') {
                // just check all values exist including duplicates
                foreach($testarr as $val) {
                    if (false === $key = array_search($val, $testcontainer)) {
                        $result->value = false;
                        return $result;
                    }
                    unset($testcontainer[$key]);
                }
                $result->value = true;
                return $result;
            } else if ($this->cardinality == 'ordered') {
                // check that subsequence is strict
                $possiblestarts = array_keys($testcontainer, $testarr[0]);
                if (empty($possiblestarts)) {
                    $result->value = false;
                    return $result;
                }
                foreach($possiblestarts as $start) {
                    for($i = 0; $i < count($testarr); $i++) {
                        // We've reached the end of the container array
                        if ($start + $i >= count($testcontainer)) {
                            $result->value = false;
                            return $result;
                        }
                        if ($testarr[$i] != $testcontainer[$start + $i]) {
                            continue 2; // try next start
                        }
                    }
                    $result->value = true;
                    return $result;
                }
                $result->value = false;
                return $result;
            }

        }
    }

    public function substring($biggerstring, $casesensitive = true) {
        $result = new QTIVariable('single', 'boolean');
        if ($casesensitive) {
            $result->value = (strpos($biggerstring->value, $this->value) !== false);
        } else {
            $result->value = (stripos($biggerstring->value, $this->value) !== false);
        }
        return $result;
    }

    public function not() {
        $result = clone($this);
        if ($this->_isNull()) {
            $result->value = null;
        } else {
            $result->value = !($this->value);
        }
        return $result;
    }

    // Underscore at end because "and" is a reserved word
    public static function and_() {
        $result = new QTIVariable('single', 'boolean', array('value' => true));
        $params = func_get_args();
        // Allow a single array as well as a parameter list
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }
        foreach($params as $param) {
            if (!$param->value) {
                $result->value = false;
                return $result;
            }
        }
        return $result;
    }

    // Underscore at end because "or" is a reserved word
    public static function or_() {
        $result = new QTIVariable('single', 'boolean');
        $params = func_get_args();
        // Allow a single array as well as a parameter list
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }
        foreach($params as $param) {
            if($param->_isNull()) {
                return $result;
            }
            if ($param->value) {
                $result->value = true;
                return $result;
            }
        }
        $result->value = false;
        return $result;
    }

    /**
     * anyN(min, max, [boolean1], [boolean2]...)
     */
    public static function anyN() {
        $result = new QTIVariable('single', 'boolean');
        $params = func_get_args();
        $min = array_shift($params);
        $max = array_shift($params);

        // Allow a single array as well as a parameter list
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }
        $false = $true = $null = 0;
        foreach($params as $param) {
            if ($param->_isNull()) {
                $null++;
            } else if ($param->value == true) {
                $true++;
            } else if ($param->value == false) {
                $false++;
            }
        }

        if ($false > (count($params) - $min)) {
            $result->value = false;
        } else if ($true > $max) {
            $result->value = false;
        } else if (($min <= $true) && ($true <= $max)) {
            $result->value = true;
        }

        return $result;
    }

    public function match($othervariable) {
        $result = new QTIVariable('single', 'boolean', array('value' => false));

        if($this->_isNull() || $othervariable->_isNull()) {
        	$result->value = null;
        	return $result;
        }
        
        // TODO: Is it OK just to let PHP decide if two values are equal?
        if (!is_array($this->value) && !(is_array($othervariable->value))) {
            $result->value = ($this->value == $othervariable->value);
            return $result;
        }
        if (count($this->value) != count($othervariable->value)) {
            $result->value = false;
            return $result;
        }
        // If it's multiple just do a diff
        if ($this->cardinality == 'multiple') {
            $result->value = (count(array_diff($this->value, $othervariable->value)) == 0);
        } else if ($this->cardinality == 'ordered') {
            // check them pairwise
            for($i = 0; $i < count($this->value); $i++) {
                if ($this->value[$i] != $othervariable->value[$i]) {
                    $result->value = false;
                    return $result;
                }
            }
            $result->value = true;
        }

        // default to false
        return $result;
    }

    public function stringMatch($othervariable, $caseSensitive, $substring = false) {
        $result = new QTIVariable('single', 'boolean', array('value' => false));

        if ($this->_isNull() || $othervariable->_isNull()) {
            $result->value = null;
            return $result;
        }

        $string1 = $this->value;
        $string2 = $othervariable->value;

        if (!$caseSensitive) {
            $string1 = strtolower($string1);
            $string2 = strtolower($string2);
        }

        if ($substring) {
            $result->value = (strpos($string1, $string2) !== false);
        } else {
            $result->value =  ($string1 == $string2);
        }

        return $result;
    }

    // TODO: Is PCRE compatible with the XML Schema regexes used in the spec?
    public function patternMatch($pattern) {
        $result = new QTIVariable('single', 'boolean', array('value' => false));

        if ($this->_isNull()) {
            $result->value = null;
            return result;
        }

        // If the pattern contains a percent it should be escaped
        $pattern = str_replace('%', '\%', $pattern);
        $result->value = (preg_match('%' . $pattern . '%', $this->value) > 0);
        return $result;
    }

    // TODO: Implement these methods
    /*
     * I'm having trouble understanding this expression. It looks from the spec as if
     * the XML should have two tolerance attributes if the mode is absolute
     * or relative. If I understand it correctly, this means an xs:list, which is
     * a whitespace-separated list in a single attribute.
     * 
     * Also, all the example items which use equal only use the exact toleranceMode 
     * so I can't find an example to work from.
     * 
     * The tolerance can also be a variable reference (in curly brackets), so we
     * may need a way for a variable to access the value of other variables. This
     * doesn't fit well with the way this class has been implemented, but can probably
     * be done.
     * 
     * Implementing as per my understanding, so may not be completely accurate!
     */
    public function equal(QTIVariable $other, $toleranceMode = 'exact', $tolerance = null, $includeLowerBound = true, $includeUpperBound = true) {
    $result = new QTIVariable('single', 'boolean');
        if($this->_isNull() || $other->_isNull()) {
            return $result;
        }
        if ($toleranceMode == 'exact') {
            $result->value = ($this->value == $other->value);
            return $result;
        } else {
            if (is_null($tolerance)) {
                throw new \Exception("tolerance must be provided");
            }
            if (is_array($tolerance)) {
                $t0 = $tolerance[0];
                $t1 = $tolerance[1];
            } else {
                $t0 = $tolerance;
                $t1 = $tolerance;
            }
            
            $result->setValue(true);
            
            if ($toleranceMode == 'absolute') {
                if ($includeLowerBound) {
                    if ($other->value < $this->value - $t0) {
                        $result->setValue(false);
                    }
                } else {
                    if ($other->value <= $this->value - $t0) {
                        $result->setValue(false);
                    }
                }
                if ($includeUpperBound) {
                    if ($other->value > $this->value + $t1) {
                        $result->setValue(false);
                    }
                } else {
                    if ($other->value >= $this->value + $t1) {
                        $result->setValue(false);
                    }
                }
            } else if ($toleranceMode == 'relative') {
                if ($includeLowerBound) {
                    if ($other->value < ($this->value * (1  - $t0 / 100))) {
                        $result->setValue(false);
                    }
                } else {
                    if ($other->value <= ($this->value * (1  - $t0 / 100))) {
                        $result->setValue(false);
                    }
                }
                if ($includeUpperBound) {
                    if ($other->value > ($this->value * (1  + $t1 / 100))) {
                        $result->setValue(false);
                    }
                } else {
                    if ($other->value >= ($this->value * (1  + $t1 / 100))) {
                        $result->setValue(false);
                    }
                }
            }
            
            return $result;
        }
        
    }
    
    public function roundTo($figures, $roundingMode='significantFigures') {
        $result = new QTIVariable('single', 'float');
        
        if($this->_isNull()) {
            return $result;
        }
        if ($roundingMode == 'significantFigures') {
            $resultValue = $this->value * pow(10, $figures - 1);
            $resultValue = round($resultValue, 0, PHP_ROUND_HALF_UP);
            $resultValue /= pow(10, $figures - 1);
            $result->value = $resultValue;
            return $result;
        } else if ($roundingMode == 'decimalPlaces') {
            $thisRounded = round($this->value, $figures, PHP_ROUND_HALF_UP);
            $result->value = $thisRounded;
            return $result;
        }
        throw new \Exception("Invalid rounding mode $roundingMode. Only significantFigures or decimalPlaces supported.");
    }

    /**
     * Check if two variables are the same if rounded.
     * 
     * @todo I'm not at all sure this is a correct implementation. It passes 
     * the test given in the spec, but needs checked.
     * 
     * @param QTIVariable $other the variable to test this against
     * @param integer $figures the number of significant figures or decimal places
     * @param string $roundingMode either significantFigures or decimalPlaces
     * @throws \Exception on invalid roundingMode
     * @return \PHPQTI\Runtime\Processing\QTIVariable single boolean result
     */
    public function equalRounded(QTIVariable $other, $figures, $roundingMode='significantFigures') {
        $result = new QTIVariable('single', 'boolean');
        if($this->_isNull() || $other->_isNull()) {
            return $result;
        }
        $result->value = ($this->roundTo($figures, $roundingMode) == $other->roundTo($figures, $roundingMode));
        return $result;
    }

    // TODO: Implement poly (and maybe ellipse, although deprecated)
    /* Note: we don't implement the "default" shape here as it's expected
    * that calling code will create a rect from the associated image dimensions
    * and call this function using that as the shape.
    */
    public function inside($shape, $coords) {
        $coordsarray = array();
        foreach(explode(',', $coords) as $coord) {
            $coordsarray[] = trim($coord);
        }

        $result = new QTIVariable('single', 'boolean');

        if ($this->_isNull()) {
            return $result;
        }

        $result->value = false;

        $values = $this->value;
        if (!is_array($values)) {
            $values = array($values);
        }

        foreach($values as $value) {
            list($pointx, $pointy) = explode(' ', $value);

            switch($shape) {
                case 'rect':
                    if (($coordsarray[0] <= $pointx)
                    && ($coordsarray[1] >= $pointy)
                    && ($coordsarray[2] >= $pointx)
                    && ($coordsarray[3] <= $pointy)) {
                        $result->value = true;
                        return $result;
                    }
                    break;
                case 'circle':
                    // work out distance of point from centre
                    $xoffset = abs($pointx - $coordsarray[0]);
                    $yoffset = abs($pointy - $coordsarray[1]);
                    $distance = sqrt(pow($xoffset, 2) + pow($yoffset, 2));
                    // if lte radius return true
                    if ($distance <= $coordsarray[2]) {
                        $result->value = true;
                        return $result;
                    }
                    break;
                case 'poly':
                    throw new \Exception("inside poly not implemented");
                    break;
                case 'ellipse':
                    throw new \Exception("inside ellipse not implemented");
                    break;
                case 'default':
                    throw new \Exception("inside default not implemented - please call with a rect instead");
                    break;
                default:
                    break;
            }
        }

        return $result;
    }

    public function lt($othervariable) {
        $result = new QTIVariable('single', 'boolean', array('value' => false));

        if ($this->_isNull() || $othervariable->_isNull()) {
            $result->value = null;
            return result;
        }

        $result->value = ($this->value < $othervariable->value);
        return $result;
    }

    public function gt($othervariable) {
        $result = new QTIVariable('single', 'boolean', array('value' => false));

        if ($this->_isNull() || $othervariable->_isNull()) {
            $result->value = null;
            return result;
        }

        $result->value = ($this->value > $othervariable->value);
        return $result;
    }

    public function lte($othervariable) {
        $result = new QTIVariable('single', 'boolean', array('value' => false));

        if ($this->_isNull() || $othervariable->_isNull()) {
            $result->value = null;
            return result;
        }

        $result->value = ($this->value <= $othervariable->value);
        return $result;
    }

    public function gte($othervariable) {
        $result = new QTIVariable('single', 'boolean', array('value' => false));

        if ($this->_isNull() || $othervariable->_isNull()) {
            $result->value = null;
            return result;
        }

        $result->value = ($this->value >= $othervariable->value);
        return $result;
    }

    // TODO: Implement these functions
    public function durationLT() {
        throw new \Exception("Not implemented");
    }

    public function durationGTE() {
        throw new \Exception("Not implemented");
    }

    public static function sum() {
        $params = func_get_args();
        // Allow a single array as well as a parameter list
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }
        $result = clone($params[0]); // There should always be one
        $result->value = 0;

        foreach($params as $param) {
            if($param->_isNull()) {
                $result->value = null;
                return $result;
            }

            $result->value += $param->value;
        }

        return $result;
    }

    public static function product() {
        $params = func_get_args();
        // Allow a single array as well as a parameter list
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }
        $result = clone(array_shift($params)); // There should always be one
        
        foreach($params as $param) {
            if($param->_isNull()) {
                $result->value = null;
                return $result;
            }

            $result->value *= $param->value;
        }

        return $result;
    }

    public function subtract($othervariable) {
        $result = clone($this);

        if ($this->_isNull() || $othervariable->_isNull()) {
            $result->value = null;
            return $result;
        }

        $result->value = $this->value - $othervariable->value;
        return $result;
    }

    public function divide($othervariable) {
        $result = new QTIVariable('single', 'float');

        if ($this->_isNull() || $othervariable->_isNull() || $othervariable->value == 0) {
            $result->value = null;
            return $result;
        }

        $result->value = $this->value / $othervariable->value;
        return $result;
    }

    public function power($othervariable) {
        $result = clone($this);

        if ($this->_isNull() || $othervariable->_isNull() || $othervariable->value == 0) {
            $result->value = null;
            return $result;
        }

        $result->value = pow($this->value, $othervariable->value);
        return $result;
    }

    public function integerDivide($othervariable) {
        if ($this->_isNull() || $othervariable->_isNull()) {
            return new QTIVariable('single', 'integer');
        }
        $result = $this->divide($othervariable);
        $result->value = floor($result->value);
        return $result;
    }

    public function integerModulus($othervariable) {
        $result = clone($this);

        if ($this->_isNull() || $othervariable->_isNull() || $othervariable->value == 0) {
            $result->value = null;
            return $result;
        }

        $result->value = $this->value % $othervariable->value;
        return $result;
    }

    public function truncate() {
        $result = new QTIVariable('single', 'integer');

        if ($this->_isNull()) {
            return $result;
        }

        if ($this->value > 0) {
            $result->value = floor($this->value);
        } else {
            $result->value = ceil($this->value);
        }
        return $result;
    }

    public function round() {
        $result = new QTIVariable('single', 'integer');

        if ($this->_isNull()) {
            return $result;
        }

        $result->value = round($this->value, 0, PHP_ROUND_HALF_DOWN);

        return $result;
    }

    public function integerToFloat() {
        $result = clone($this);
        $result->type = 'float';
        return $result;
    }

    public function customOperator() {
        throw new \Exception("Not implemented");
    }



    // Return a QTIVariable representing the default
    public function getDefaultValue() {
        return new QTIVariable($this->cardinality, $this->type, array('value' => $this->defaultValue));
    }



    /**
     * Set the value of the variable
     * @param QTIVariable|array|string|boolean $value The value as a QTIVariable
     */
    public function setValue($value) {
        if ($value instanceof QTIVariable) {
            $this->value = $value->value;
        } else if (is_string($value) || is_bool($value)) {
            if ($this->cardinality == 'single') {
                $this->value = $value;
            } else {
                $this->value = array($value);
            }
        } else if (is_array($value) && !$this->cardinality == 'single') {
            $this->value = $value;
        } else {
            throw new \Exception('invalid value');
        }
    }

    public function getValue() {
        return $this->value;
    }
    
    /*
     * Getter for record type variables. For these variables, 
     * the value array is an associative one, and values are other
     * single cardinality variables (they have to be because we 
     * need to retain the baseType of the value - see "value" element
     * definition in spec).
     */
    public function getFieldValue($fieldid) {
    	if (isset($this->field[$fieldid])) {
    		return $this->field[$fieldid];
    	} else {
    		return null;
    	}
    }
    
    public function setFieldValue($fieldid, QTIVariable $variable) {
    	$this->field[$fieldid] = $variable;
    }

    /**
     * Set the correctResponse of the variable
     * @param QTIVariable $value The value as a QTIVariable
     */
    public function setCorrectResponse($value) {
        $this->correctResponse = $value->value;
    }

    // Return a QTIVariable representing the correct value
    public function getCorrectResponse() {
        return new QTIVariable($this->cardinality, $this->type, array('value' => $this->correctResponse));
    }


}