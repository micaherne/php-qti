<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class IntegerToFloat extends \PHPQTI\Model\Gen\IntegerToFloat implements Expression {

    protected $_elementName = 'integerToFloat';

    public function __invoke($controller) {
        $val1 = $this->_children[0]($controller);
    
        return $val1->integerToFloat();
    }
}