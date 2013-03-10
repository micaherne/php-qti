<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Contains extends \PHPQTI\Model\Gen\Contains implements Expression {

    protected $_elementName = 'contains';

    public function __invoke($controller) {
        $var1 = $this->_children[0]($controller);
        $var2 = $this->_children[1]($controller);
        return $var1->contains($var2);
    }
}