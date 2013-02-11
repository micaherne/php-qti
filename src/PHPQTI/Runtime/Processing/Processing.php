<?php

namespace PHPQTI\Runtime\Processing;

/*
 * TODO: The creation of the expression closures and classes should probably be refactored out into
* a qti_expression_factory class, or something like that.
*/
abstract class Processing {

    protected $controller;

    protected $processingFunction;

    public function __construct(ItemController $controller) {
        $this->controller = $controller;
    }

    /**
     * Magic function to simplify creating processing methods. If the first string
     * passed to the function is an array, it will be assumed to be an associative
     * array of attribute name/value pairs, otherwise an empty attribute array will
     * be passed to the underlying method.
     *
     * e.g. __call('test', array('id' => 12), object1, object2) will cause the following
     * method call: _test(array('id' => 12), object1, object2)
     * whereas __call('test', object1, object2) will cause the following:
     * _test(array(), object1, object2)
     *
     * This is because most processing instructions don't need attributes, but it could
     * be a source of bugs if we had to remember to generate an empty array each time.
     * @param unknown_type $name
     * @param unknown_type $args
     * @throws Exception
     */
    public function __call($name, $args) {
        $realmethodname = "_$name";
        if (method_exists($this, $realmethodname)) {
            if (count($args) > 0 && is_array($args[0])) {
                $attrs = array_shift($args);
            } else {
                $attrs = array();
            }
            return $this->$realmethodname($attrs, $args);
        }
        throw new Exception("qti_response_processing method _$name not found");
    }

    public function __text($text) {
        return function($controller) use ($text) {
            return $text;
        };
    }

    public function execute() {
        if ($this->processingFunction) { // there may be no processing (e.g. extended_text.xml)
            $this->processingFunction->__invoke($this->controller);
        }
    }

    /*
     * 8.2. Generalized Response Processing
    */

    public function _responseProcessing($attrs, $children) {
        $this->processingFunction = function($controller) use($children) {
            foreach($children as $child) {
                $child->__invoke($controller);
            }
        };
    }

    public function _responseCondition($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            foreach($children as $child) {
                $result = $child->__invoke($controller);
                if (isset($result->value) && $result->value === true) {
                    return;
                }
            }
        };
    }

    public function _responseIf($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $result = $children[0]->__invoke($controller);
            if ($result->value === true) {
                for($i = 1; $i < count($children); $i++) {
                    $children[$i]->__invoke($controller);
                }
            }
            return $result;
        };
    }

    public function _responseElseIf($attrs, $children) {
        // Identical to responseIf
        return function($controller) use ($attrs, $children) {
            $result = $children[0]->__invoke($controller);
            if ($result->value === true) {
                for($i = 1; $i < count($children); $i++) {
                    $children[$i]->__invoke($controller);
                }
            }
            return $result;
        };
    }

    public function _responseElse($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            for($i = 0; $i < count($children); $i++) {
                $children[$i]->__invoke($controller);
            }
        };
    }

    public function _setOutcomeValue($attrs, $children) {
        return function($controller) use($attrs, $children) {
            $varname = $attrs['identifier'];
            $controller->outcome[$varname]->setValue($children[0]->__invoke($controller));
        };
    }

    public function _lookupOutcomeValue($attrs, $children) {
        throw new Exception("Not implemented");
    }

    /*
     * 10.3 Template Processing
    */

    public function _templateProcessing($attrs, $children) {
        $this->processingFunction = function($controller) use($children) {
            foreach($children as $child) {
                $child->__invoke($controller);
            }
        };
    }

    public function _templateCondition($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            foreach($children as $child) {
                $result = $child->__invoke($controller);
                if ($result->value === true) {
                    return;
                }
            }
        };
    }

    public function _templateIf($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $result = $children[0]->__invoke($controller);
            if ($result->value === true) {
                for($i = 1; $i < count($children); $i++) {
                    $children[$i]->__invoke($controller);
                }
            }
            return $result;
        };
    }

    public function _templateElseIf($attrs, $children) {
        // Identical to templateIf
        return function($controller) use ($attrs, $children) {
            $result = $children[0]->__invoke($controller);
            if ($result->value === true) {
                for($i = 1; $i < count($children); $i++) {
                    $children[$i]->__invoke($controller);
                }
            }
            return $result;
        };
    }

    public function _templateElse($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            for($i = 0; $i < count($children); $i++) {
                $children[$i]->__invoke($controller);
            }
        };
    }

    public function _setTemplateValue($attrs, $children) {
        return function($controller) use($attrs, $children) {
            $varname = $attrs['identifier'];
            $controller->template[$varname]->setValue($children[0]->__invoke($controller));
        };
    }

    public function _setCorrectResponse($attrs, $children) {
        return function($controller) use($attrs, $children) {
            $varname = $attrs['identifier'];
            $controller->response[$varname]->setCorrectResponse($children[0]->__invoke($controller));
        };
    }

    // TODO: Implement setDefaultValue and exitTemplate

    /*
     * 15.1. Built-in General Expressions
    */

    public function _baseValue($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            return new qti_variable('single', $attrs['baseType'], array(
                    'value' => $children[0]($controller)
            ));
        };
    }

    public function _variable($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $varname = $attrs['identifier'];
            if(isset($controller->response[$varname])) {
                return $controller->response[$varname];
            } else if (isset($controller->outcome[$varname])) {
                return $controller->outcome[$varname];
            } else if (isset($controller->template[$varname])) {
                return $controller->template[$varname];
            } else {
                throw new qti_processing_exception("Variable $varname not found");
            }
        };
    }

    public function _default($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $varname = $attrs['identifier'];
            if(isset($controller->response[$varname])) {
                return $controller->response[$varname]->getDefaultValue();
            } else if (isset($controller->outcome[$varname])) {
                return $controller->outcome[$varname]->getDefaultValue();
            } else {
                throw new qti_processing_exception("Variable $varname not found");
            }
        };
    }

    public function _correct($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $varname = $attrs['identifier'];
            if(isset($controller->response[$varname])) {
                return $controller->response[$varname]->getCorrectResponse();
            } else {
                throw new qti_processing_exception("Variable $varname not found");
            }
        };

    }

    public function _mapResponse($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $varname = $attrs['identifier'];
            if(isset($controller->response[$varname])) {
                return $controller->response[$varname]->mapResponse();
            } else {
                throw new qti_processing_exception("Variable $varname not found");
            }
        };
    }

    public function _mapResponsePoint($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $varname = $attrs['identifier'];
            if(isset($controller->response[$varname])) {
                return $controller->response[$varname]->mapResponsePoint();
            } else {
                throw new qti_processing_exception("Variable $varname not found");
            }
        };
    }

    public function _null($attrs, $children) {
        // Create as single identifier, although it can be matched against any other null
        return function($controller) use ($attrs, $children) {
            return new qti_variable('single', 'identifier', array(
                    'value' => null
            ));
        };
    }

    public function _randomInteger($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $min = $attrs['min'];
            $max = $attrs['max'];
            $step = isset($attrs['step']) ? $attrs['step'] : 1;

            $offsetmax = intval($max/$step);
            $value = $min + mt_rand(0, $offsetmax);
            return new qti_variable('single', 'integer', array(
                    'value' => $value
            ));
        };
    }

    public function _randomFloat($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $min = $attrs['min'];
            $max = $attrs['max'];

            $value = $randomfloat = $min + mt_rand() / mt_getrandmax() * ($max - $min);
            return new qti_variable('single', 'float', array(
                    'value' => $value
            ));
        };
    }

    /*
     * TODO: Implement
    * 15.2. Expressions Used only in Outcomes Processing
    */

    /*
     * 15.3. Operators
    */
    public function _multiple($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $vars = array();
            foreach($children as $child) {
                $vars[] = $child->__invoke($controller);
            }
            return qti_variable::multiple($vars);
        };
    }

    public function _ordered($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $vars = array();
            foreach($children as $child) {
                $vars[] = $child->__invoke($controller);
            }
            return qti_variable::ordered($vars);
        };
    }

    public function _containerSize($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $container = $child->__invoke($controller);
            return $container->containerSize();
        };
    }

    public function _isNull($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $what = $children[0]->__invoke($controller);
            return $what->isNull();
        };
    }

    public function _index($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $what = $children[0]->__invoke($controller);
            return $what->index($attrs['n']);
        };
    }

    public function _fieldValue($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $what = $children[0]->__invoke($controller);
            return $what->fieldValue($attrs['fieldIdentifier']);
        };
    }

    public function _random($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $what = $children[0]->__invoke($controller);
            return $what->random();
        };
    }

    public function _member($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $var1 = $children[0]->__invoke($controller);
            $var2 = $children[1]->__invoke($controller);
            return $var1->member($var2);
        };
    }

    public function _delete($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $var1 = $children[0]->__invoke($controller);
            $var2 = $children[1]->__invoke($controller);
            return $var1->delete($var2);
        };
    }

    public function _contains($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $var1 = $children[0]->__invoke($controller);
            $var2 = $children[1]->__invoke($controller);
            return $var1->contains($var2);
        };
    }

    public function _substring($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $var1 = $children[0]->__invoke($controller);
            $var2 = $children[1]->__invoke($controller);
            return $var1->substring($var2, $attrs['caseSensitive']);
        };
    }

    public function _not($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $var1 = $children[0]->__invoke($controller);
            return $var1->not();
        };
    }

    public function _and($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $vars = array();
            foreach($children as $child) {
                $vars[] = $child->__invoke($controller);
            }
            return qti_variable::and_($vars);
        };
    }

    public function _or($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $vars = array();
            foreach($children as $child) {
                $vars[] = $child->__invoke($controller);
            }
            return qti_variable::or_($vars);
        };
    }

    public function _anyN($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $vars = array();
            foreach($children as $child) {
                $vars[] = $child->__invoke($controller);
            }
            return qti_variable::anyN($attrs['min'], $attrs['max'], $vars);
        };
    }

    public function _match($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);
            $val2 = $children[1]->__invoke($controller);

            return $val1->match($val2);
        };
    }

    public function _stringMatch($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);
            $val2 = $children[1]->__invoke($controller);

            // TODO: Missing substring attribute will probably break helper function
            return $val1->stringMatch($val2, $attrs['caseSensitive'], $attrs['substring']);
        };
    }

    public function _patternMatch($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);

            return $val1->patternMatch($attrs['pattern']);
        };
    }

    public function _equal($attrs, $children) {
        throw new Exception("Not implemented");
    }

    public function _equalRounded($attrs, $children) {
        throw new Exception("Not implemented");
    }

    public function _inside($attrs, $children) {
        throw new Exception("Not implemented");
    }

    public function _lt($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);
            $val2 = $children[1]->__invoke($controller);

            return $val1->lt($val2);
        };
    }

    public function _gt($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);
            $val2 = $children[1]->__invoke($controller);

            return $val1->gt($val2);
        };
    }

    public function _lte($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);
            $val2 = $children[1]->__invoke($controller);

            return $val1->lte($val2);
        };
    }

    public function _gte($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);
            $val2 = $children[1]->__invoke($controller);

            return $val1->gte($val2);
        };
    }

    public function _durationLT($attrs, $children) {
        throw new Exception("Not implemented");
    }

    public function _durationGTE($attrs, $children) {
        throw new Exception("Not implemented");
    }

    public function _sum($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $vars = array();
            foreach($children as $child) {
                $vars[] = $child->__invoke($controller);
            }
            return qti_variable::sum($vars);
        };
    }

    public function _product($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $vars = array();
            foreach($children as $child) {
                $vars[] = $child->__invoke($controller);
            }
            return qti_variable::product($vars);
        };
    }

    public function _subtract($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);
            $val2 = $children[1]->__invoke($controller);

            return $val1->subtract($val2);
        };
    }

    public function _divide($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);
            $val2 = $children[1]->__invoke($controller);

            return $val1->divide($val2);
        };
    }

    public function _power($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);
            $val2 = $children[1]->__invoke($controller);

            return $val1->power($val2);
        };
    }

    public function _integerDivide($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);
            $val2 = $children[1]->__invoke($controller);

            return $val1->integerDivide($val2);
        };
    }

    public function _integerModulus($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);
            $val2 = $children[1]->__invoke($controller);

            return $val1->integerModulus($val2);
        };
    }

    public function _truncate($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);

            return $val1->truncate();
        };
    }

    public function _round($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);

            return $val1->round();
        };
    }

    public function _integerToFloat($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $val1 = $children[0]->__invoke($controller);

            return $val1->integerToFloat();
        };
    }

    public function _customOperator($attrs, $children) {
        throw new Exception("Not implemented");
    }

}