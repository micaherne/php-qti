<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Power extends \PHPQTI\Model\Gen\Power implements Expression {

    protected $_elementName = 'power';

    public function __invoke($controller) {
        $val1 = $this->_children[0]($controller);
        $val2 = $this->_children[1]($controller);
    
        return $val1->power($val2);
    }
}