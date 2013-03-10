<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Lte extends \PHPQTI\Model\Gen\Lte implements Expression {

    protected $_elementName = 'lte';

    public function __invoke($controller) {
        $val1 = $this->_children[0]($controller);
        $val2 = $this->_children[1]($controller);
    
        return $val1->lte($val2);
    }
}