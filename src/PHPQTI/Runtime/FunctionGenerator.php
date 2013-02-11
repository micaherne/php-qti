<?php

namespace PHPQTI\Runtime;

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
            return qti_item_body::__basicElement('img', $attrs, $args);
        };
    }
    
    public function _object($attrs, $args) {
        return function($controller) use ($attrs, $args) {
            if(isset($attrs['data'])) {
                $attrs['data'] = $controller->resource_provider->urlFor($attrs['data']);
            }
            return qti_item_body::__basicElement('object', $attrs, $args);
        };
    }

    public function _itemBody($attrs, $children) {
        $this->displayFunction = function($controller) use($children) {
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

}