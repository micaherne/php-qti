<?php

namespace PHPQTI\Runtime\Processing;

class Variable {

    public $cardinality;
    public $type;
    // For response vars, QTI has a candidateResponse wrapper for the value - any reason to implement?
    public $value;
    public $correctResponse = null;
    public $defaultValue = null;
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


    public function __toString(){
        return $this->cardinality . ' ' . $this->type . ' [' . $this->valueAsString() . ']';
    }

    public function valueAsString() {
        return (is_array($this->value) ? implode(',', $this->value) : $this->value);
    }

    // Implement mapResponse processing here because it's sensible!
    // TODO: Implement lower and upper bound
    // TODO: Should probably be in the Processing\Mapping class instead
    public function mapResponse() {
        if (is_null($this->mapping)) {
        	throw new ProcessingException('Mapping required for mapResponse()');
        }
        if ($this->cardinality == 'single') {
            if (array_key_exists($this->value, $this->mapping->mapEntry)) {
                $value = $this->mapping->mapEntry[$this->value];
            } else {
                $value = $this->mapping->defaultValue;
            }
        } else {
            $value = 0;
            // array_unique used because values should only be counted once - see mapResponse documentation
            foreach(array_unique($this->value) as $response) {
                if (array_key_exists($response, $this->mapping->mapEntry)) {
                    $value += $this->mapping->mapEntry[$response];
                } else if ($this->type == 'pair') {  // Check pair opposite way round
                    $responseReversed = implode(' ', array_reverse(explode(' ', $response)));
                    if (array_key_exists($responseReversed, $this->mapping->mapEntry)) {
                        $value += $this->mapping->mapEntry[$responseReversed];
                    } else {
                        $value += $this->mapping->defaultValue;
                    }
                } else {
                    $value += $this->mapping->defaultValue;
                }

            }
        }

        return new Variable('single', 'float', array('value' => $value));
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
                $testvar = new Variable('single', 'point', array('value' => $value));
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

        return new Variable('single', 'float', array('value' => $resultvalue));
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
    *
    * There is a distinction between the notion of a variable and an expression.
    * In theory, most of these functions apply to expressions in the spec. However,
    * in this implementation expressions are translated into closures / classes which,
    * when invoked, produce a variable as a result, so it makes a certain amount
    * of sense to implement these functions in the Variable class.
    *
    * In other words, these functions should not be thought of as directly related to the
    * expressions with the same name in the spec. The closures and classes produced by
    * Response_processing are the implementation of expressions, which just happen to
    * use these functions to do their work.
    *
    * Update: today I'm thinking that the closures and classes used in response processors
    * should really be thought of as "expression processing functions" rather than expressions
    * per se. So the following methods are "operator helper methods" and will be used when
    * creating the expression processors. As I understand it, an expression always evaluates to a variable
    * (i.e. when the processing function is executed)
    */

    // TODO: Should we implement Built-in General Expressions here? At the moment
    // they're just implemented directly in Response_processing

    /*
     * 15.3 Operators
    */
    public static function multiple() {
        $params = func_get_args();

        // Null if no arguments passed
        if (count($params) == 0) {
            return new Variable('multiple', 'identifier');
        } else {
            $result = new Variable('multiple', 'identifier', array('value' => array()));
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
        $result = forward_static_call_array('\PHPQTI\Runtime\Processing\Variable::multiple', $params);
        $result->cardinality = 'ordered';
        return $result;
    }

    public function containerSize() {
        $result = new Variable('single', 'integer', array('value' => 0));
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
        $result = new Variable('single', 'boolean', array('value' => $this->_isNull()));
        return $result;
    }

    public function index($i) {
        $result = new Variable('single', $this->type);
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
        $result = new Variable('single', 'boolean');
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
        $result = new Variable('single', 'boolean');
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
        $result = new Variable('single', 'boolean');
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
        $result = new Variable('single', 'boolean', array('value' => true));
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
        $result = new Variable('single', 'boolean', array('value' => false));
        $params = func_get_args();
        // Allow a single array as well as a parameter list
        if (count($params) == 1 && is_array($params[0])) {
            $params = $params[0];
        }
        foreach($params as $param) {
            if ($param->value) {
                $result->value = true;
                return $result;
            }
        }
        return $result;
    }

    /**
     * anyN(min, max, [boolean1], [boolean2]...)
     */
    public static function anyN() {
        $result = new Variable('single', 'boolean');
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
        $result = new Variable('single', 'boolean', array('value' => false));

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
        $result = new Variable('single', 'boolean', array('value' => false));

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
        $result = new Variable('single', 'boolean', array('value' => false));

        if ($this->_isNull()) {
            $result->value = null;
            return result;
        }

        // TODO: What if the pattern contains a percent? Should be escaped
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
    public function equal() {
        throw new \Exception("Not implemented");
    }

    /**
     * Check if two variables are the same if rounded.
     * 
     * @todo I'm not at all sure this is a correct implementation. It passes 
     * the test given in the spec, but needs checked.
     * 
     * @param Variable $other the variable to test this against
     * @param integer $figures the number of significant figures or decimal places
     * @param string $roundingMode either significantFigures or decimalPlaces
     * @throws \Exception on invalid roundingMode
     * @return \PHPQTI\Runtime\Processing\Variable single boolean result
     */
    public function equalRounded(Variable $other, $figures, $roundingMode='significantFigures') {
        $result = new Variable('single', 'boolean');
        if($this->_isNull() || $other->_isNull()) {
            return $result;
        }
        if ($roundingMode == 'significantFigures') {
            $figuresThis = $this->value * pow(10, $figures - 1);
            $figuresThis = round($figuresThis, 0, PHP_ROUND_HALF_UP);
            $figuresThis /= pow(10, $figures);
            $figuresOther = $other->value * pow(10, $figures - 1);
            $figuresOther = round($figuresOther, 0, PHP_ROUND_HALF_UP);
            $figuresOther /= pow(10, $figures);
            $result->value = ($figuresThis == $figuresOther);
            return $result;
        } else if ($roundingMode == 'decimalPlaces') {
            $thisRounded = round($this->value, $figures, PHP_ROUND_HALF_UP);
            $otherRounded = round($other->value, $figures, PHP_ROUND_HALF_UP);
            $result->value = ($thisRounded == $otherRounded);
            return $result;
        }
        throw new \Exception("Invalid rounding mode $roundingMode. Only significantFigures or decimalPlaces supported.");
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

        $result = new Variable('single', 'boolean');

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
        $result = new Variable('single', 'boolean', array('value' => false));

        if ($this->_isNull() || $othervariable->_isNull()) {
            $result->value = null;
            return result;
        }

        $result->value = ($this->value < $othervariable->value);
        return $result;
    }

    public function gt() {
        $result = new Variable('single', 'boolean', array('value' => false));

        if ($this->_isNull() || $othervariable->_isNull()) {
            $result->value = null;
            return result;
        }

        $result->value = ($this->value > $othervariable->value);
        return $result;
    }

    public function lte() {
        $result = new Variable('single', 'boolean', array('value' => false));

        if ($this->_isNull() || $othervariable->_isNull()) {
            $result->value = null;
            return result;
        }

        $result->value = ($this->value <= $othervariable->value);
        return $result;
    }

    public function gte() {
        $result = new Variable('single', 'boolean', array('value' => false));

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
        $result = clone($params[0]); // There should always be one
        $result->value = 0;

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
        $result = new Variable('single', 'float');

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
        $result = $this->divide($othervariable);
        $result->value = round($result->value);
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
        $result = new Variable('single', 'integer');

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
        $result = new Variable('single', 'integer');

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



    // Return a Variable representing the default
    public function getDefaultValue() {
        return new Variable($this->cardinality, $this->type, array('value' => $this->defaultValue));
    }



    /**
     * Set the value of the variable
     * @param Variable|array|string $value The value as a Variable
     */
    public function setValue($value) {
        if ($value instanceof Variable) {
            $this->value = $value->value;
        } else if (is_string($value)) {
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

    /**
     * Set the correctResponse of the variable
     * @param Variable $value The value as a Variable
     */
    public function setCorrectResponse($value) {
        $this->correctResponse = $value->value;
    }

    // Return a Variable representing the correct value
    public function getCorrectResponse() {
        return new Variable($this->cardinality, $this->type, array('value' => $this->correctResponse));
    }


}