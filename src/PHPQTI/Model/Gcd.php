<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Gcd extends \PHPQTI\Model\Gen\Gcd implements Expression {

    protected $_elementName = 'gcd';

    public function __invoke($controller) {
        $vars = array();
        foreach($this->_children as $child) {
            $vars[] = $child($controller);
        }
        return QTIVariable::gcd($vars);
    }
}