<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Member extends \PHPQTI\Model\Gen\Member implements Expression {

    protected $_elementName = 'member';

    public function __invoke($controller) {
        $var1 = $this->_children[0]($controller);
        $var2 = $this->_children[1]($controller);
        return $var1->member($var2);
    }
}