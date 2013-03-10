<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Multiple extends \PHPQTI\Model\Gen\Multiple implements Expression {

    protected $_elementName = 'multiple';

    public function __invoke($controller) {
        $vars = array();
        foreach($this->_children as $child) {
            $vars[] = $child($controller);
        }
        return QTIVariable::multiple($vars);
    }
}