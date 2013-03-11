<?php

namespace PHPQTI\Util;

use PHPQTI\Model\MathML\MathMLController;

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
        
        if ($name === '__mathml') {
            return new MathMLController($args[0]);
        }
                
        die('Unknown class ' . $name);

    }
    
}