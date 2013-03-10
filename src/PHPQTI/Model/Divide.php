<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Divide extends \PHPQTI\Model\Gen\Divide implements Expression {

    protected $_elementName = 'divide';

    public function __invoke($controller) {
        $val1 = $this->_children[0]($controller);
        $val2 = $this->_children[1]($controller);
    
        return $val1->divide($val2);
    }
}