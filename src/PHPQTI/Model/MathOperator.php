<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;
use PHPQTI\Runtime\QTIVariable;

class MathOperator extends \PHPQTI\Model\Gen\MathOperator implements Expression {

    protected $_elementName = 'mathOperator';

    public function __invoke($controller) {
        $vars = array();
        foreach($this->_children as $child) {
            $vars[] = $child($controller);
        }
        return QTIVariable::mathOperator($this->name, $vars);
    }
}