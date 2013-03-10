<?php

namespace PHPQTI\Util;

use PHPQTI\Model\Base\Text;

use PHPQTI\Util\XMLUtils;

class ObjectFactory {
    
    public $_instance;
    
    public function getInstance() {
        return $this->_instance;
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
     * @throws \Exception
     */
    public function __call($name, $args) {
    
        if (count($args) > 0 && is_array($args[0])) {
            $attrs = array_shift($args);
        } else {
            $attrs = array();
        }
        
        if ($name == '__text') {
            return new Text($args[0]);
        }
        $realclassname = 'PHPQTI\\Model\\' . XMLUtils::className($name);
        if (class_exists($realclassname)) {
            $result = new $realclassname($attrs, $args);
            return $result;
        }
                
        die('Unknown class ' . $name);
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
    
}