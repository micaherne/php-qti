<?php
 
namespace PHPQTI\Model;
 
class Value extends \PHPQTI\Model\Gen\Value {

    protected $_elementName = 'value';

    public function __invoke($controller) {
        $result = '';
        foreach($this->_children as $child) { // only text elements allowed
            $result .= $child->__invoke($controller);
        }
        return $result;
    }
    
}