<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Not extends \PHPQTI\Model\Gen\Not implements Expression {

    protected $_elementName = 'not';

    public function __invoke($controller) {
        $var1 = $this->_children[0]($controller);
        return $var1->not();
    }
}