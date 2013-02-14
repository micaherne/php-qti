<?php

//    PHP-QTI - a PHP library for QTI v2.1
//    Copyright (C) 2013 Michael Aherne
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program. If not, see <http://www.gnu.org/licenses/>.

namespace PHPQTI\Runtime;

use PHPQTI\Runtime\Processing\Variable;
use PHPQTI\Runtime\Processing\ProcessingException;

/**
 * Generates a closure or other invokable class for a given QTI element.
 * 
 * @author Michael Aherne
 *
 */
class FunctionGenerator {

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
        
        if (count($args) > 0 && is_array($args[0])) {
            $attrs = array_shift($args);
        } else {
            $attrs = array();
        }
        $realclassname = 'PHPQTI\\Runtime\\Element\\' . ucfirst($name);
        if (class_exists($realclassname)) {
            return new $realclassname($attrs, $args);
        }
        $realmethodname = "_$name";
        if (method_exists($this, $realmethodname)) {
            return $this->$realmethodname($attrs, $args);
        }
        
        // Support MathML functions. (___mathml_math function 
        // exists below to create container with correct NS)
        // TODO: It would be good if this was pluggable to support other namespaces if required.
        if (strpos($name, '__mathml_') === 0) {
            $name = substr($name, 9);
        }

        // default to just creating a basic HTML element
        return $this->__default($name, $attrs, $args);
    }
    
    /**
     * Generate a function from an QTI element.
     * 
     * This is mainly intended for simplifying testing and should not necessarily be
     * relied upon for actually running a QTI item.
     * 
     * @param \DomElement $el a QTI element
     * @return object a closure or class which implements the element
     */
    public function fromXmlElement(\DomElement $el) {
    	$attrs = array();
    	foreach($el->attributes as $name => $attr) {
    		$attrs[$name] = $attr->nodeValue;
    	}
    	$args = array($attrs);
    	foreach($el->childNodes as $node) {
    		if ($node->nodeType == XML_ELEMENT_NODE) {
    			$args[] = $this->fromXmlElement($node);
    		} else if ($node->nodeType == XML_TEXT_NODE) {
    		    if (trim($node->textContent) == '') {
    		        continue;
    		    }
    			$args[] = $this->__text($node->nodeValue);
    		} else if ($node->nodeType == XML_CDATA_SECTION_NODE) {
    		    if (trim($node->textContent) == '') {
    		        continue;
    		    }
    			$args[] = $this->__text($node->nodeValue);
    		}
    	}
    	return $this->__call($el->nodeName, $args);
    }

    // Just return a function to create a basic HTML element
    public static function __default($name, $attrs, $children) {
        return function($controller) use ($name, $attrs, $children) {
            $result = "<$name";
            if(!empty($attrs)) {
                foreach($attrs as $key => $value) {
                    $result .= " $key=\"$value\"";
                }
            }
            $result .= ">";
            if(!empty($children)) {
                foreach($children as $child) {
                    $result .= $child->__invoke($controller);
                }
            }
            $result .= "</$name>";
            return $result;
        };
    }
    
    public static function __basicElement($name, $attrs, $children, $controller) {
    	$result = "<$name";
    	if(!empty($attrs)) {
    		foreach($attrs as $key => $value) {
    			$result .= " $key=\"$value\"";
    		}
    	}
    	$result .= ">";
    	if(!empty($children)) {
    		foreach($children as $child) {
    			$result .= $child->__invoke($controller);
    		}
    	}
    	$result .= "</$name>";
    	return $result;
    }

    public static function __text($text) {
        return function($controller) use ($text) {
            return $text;
        };
    }

    // TODO: These next 2 exist just to wire in the resource provider - simplify
    
    public function _img($attrs, $args) {
        return function($controller) use ($attrs, $args) {
            if(isset($attrs['src'])) {
                $attrs['src'] = $controller->resource_provider->urlFor($attrs['src']);
            }
            return FunctionGenerator::__basicElement('img', $attrs, $args, $controller);
        };
    }
    
    public function _object($attrs, $args) {
        return function($controller) use ($attrs, $args) {
            if(isset($attrs['data'])) {
                $attrs['data'] = $controller->resource_provider->urlFor($attrs['data']);
            }
            return FunctionGenerator::__basicElement('object', $attrs, $args, $controller);
        };
    }

    public function _itemBody($attrs, $children) {
        return function($controller) use($children) {
            $result = "<div";
            if(!empty($attrs)) { // add stuff like "class" attribute
                foreach($attrs as $key => $value) {
                    $result .= " $key=\"$value\"";
                }
            }
            $result .= ">";
            foreach($children as $child) {
                $result .= $child->__invoke($controller);
            }
            $result .= "</div>";
            return $result;
        };
    }
        
    // Basic printedVariable function
    // TODO: Make work for non-string types
    // TODO: Support format and base attributes
    public function _printedVariable($attrs, $children) {
        return function($controller) use ($attrs) {
            $identifier = $attrs['identifier'];
            return $controller->template[$identifier]->value;
        };
    }
    
    /* Create MathML container. Note the three underscores are required
     * as the method name generated is __mathml_math (with 2 underscores)
     */
    public function ___mathml_math($attrs, $children) {
        return function($controller) use($attrs, $children) {
            $result = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\">";
            foreach($children as $child) {
                $result .= $child->__invoke($controller);
            }
            $result .= "</math>";
            return $result;
        };
    }
    
    /*
     * 8.2. Generalized Response Processing
    */
    
    public function _responseProcessing($attrs, $children) {
        return function($controller) use($children) {
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
            return new Variable('single', $attrs['baseType'], array(
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
                throw new ProcessingException("Variable $varname not found");
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
                throw new ProcessingException("Variable $varname not found");
            }
        };
    }
    
    public function _correct($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $varname = $attrs['identifier'];
            if(isset($controller->response[$varname])) {
                return $controller->response[$varname]->getCorrectResponse();
            } else {
                throw new ProcessingException("Variable $varname not found");
            }
        };
    
    }
    
    public function _mapResponse($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $varname = $attrs['identifier'];
            if(isset($controller->response[$varname])) {
                return $controller->response[$varname]->mapResponse();
            } else {
                throw new ProcessingException("Variable $varname not found");
            }
        };
    }
    
    public function _mapResponsePoint($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $varname = $attrs['identifier'];
            if(isset($controller->response[$varname])) {
                return $controller->response[$varname]->mapResponsePoint();
            } else {
                throw new ProcessingException("Variable $varname not found");
            }
        };
    }
    
    public function _null($attrs, $children) {
        // Create as single identifier, although it can be matched against any other null
        return function($controller) use ($attrs, $children) {
            return new Variable('single', 'identifier', array(
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
            return new Variable('single', 'integer', array(
                    'value' => $value
            ));
        };
    }
    
    public function _randomFloat($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $min = $attrs['min'];
            $max = $attrs['max'];
    
            $value = $randomfloat = $min + mt_rand() / mt_getrandmax() * ($max - $min);
            return new Variable('single', 'float', array(
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
            return Variable::multiple($vars);
        };
    }
    
    public function _ordered($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $vars = array();
            foreach($children as $child) {
                $vars[] = $child->__invoke($controller);
            }
            return Variable::ordered($vars);
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
            return Variable::and_($vars);
        };
    }
    
    public function _or($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $vars = array();
            foreach($children as $child) {
                $vars[] = $child->__invoke($controller);
            }
            return Variable::or_($vars);
        };
    }
    
    public function _anyN($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $vars = array();
            foreach($children as $child) {
                $vars[] = $child->__invoke($controller);
            }
            return Variable::anyN($attrs['min'], $attrs['max'], $vars);
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
            return Variable::sum($vars);
        };
    }
    
    public function _product($attrs, $children) {
        return function($controller) use ($attrs, $children) {
            $vars = array();
            foreach($children as $child) {
                $vars[] = $child->__invoke($controller);
            }
            return Variable::product($vars);
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