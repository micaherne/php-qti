<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;
use PHPQTI\Runtime\QTIVariable;

class Gt extends \PHPQTI\Model\Gen\Gt implements Expression {

    protected $_elementName = 'gt';

    public function __invoke($controller) {
        $val1 = $this->_children[0]($controller);
        $val2 = $this->_children[1]($controller);
    
        return $val1->gt($val2);
    }
}