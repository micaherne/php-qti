<?php
 
namespace PHPQTI\Model;
 
class DefaultValue extends \PHPQTI\Model\Gen\DefaultValue {

    protected $_elementName = 'defaultValue';

    public function __invoke($controller) {
        $values = array();
        foreach($this->_children as $child) { // only value elements allowed
            $values[] = $child->__invoke($controller);
        }
        return $values;
    }
    
}