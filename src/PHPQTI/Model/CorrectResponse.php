<?php
 
namespace PHPQTI\Model;
 
class CorrectResponse extends \PHPQTI\Model\Gen\CorrectResponse {

    protected $_elementName = 'correctResponse';
    
    public function __invoke($controller) {
        $values = array();
        foreach($this->_children as $child) { // only value elements allowed
            $values[] = $child->__invoke($controller);
        }
        return $values;
    }
    
}