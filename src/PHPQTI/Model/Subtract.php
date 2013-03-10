<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Subtract extends \PHPQTI\Model\Gen\Subtract implements Expression {

    protected $_elementName = 'subtract';

    public function __invoke($controller) {
        $val1 = $this->_children[0]($controller);
        $val2 = $this->_children[1]($controller);
    
        return $val1->subtract($val2);
    }
}