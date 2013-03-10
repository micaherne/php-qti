<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class IntegerModulus extends \PHPQTI\Model\Gen\IntegerModulus implements Expression {

    protected $_elementName = 'integerModulus';

    public function __invoke($controller) {
        $val1 = $this->_children[0]($controller);
        $val2 = $this->_children[1]($controller);
    
        return $val1->integerModulus($val2);
    }
}