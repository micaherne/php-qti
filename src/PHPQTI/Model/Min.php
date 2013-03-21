<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;
use PHPQTI\Runtime\QTIVariable;

class Min extends \PHPQTI\Model\Gen\Min implements Expression {

    protected $_elementName = 'min';

    public function __invoke($controller) {
        $vars = array();
        foreach($this->_children as $child) {
            $vars[] = $child($controller);
        }
        return QTIVariable::min($vars);
    }
}