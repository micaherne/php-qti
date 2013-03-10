<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Sum extends \PHPQTI\Model\Gen\Sum implements Expression {

    protected $_elementName = 'sum';

    public function __invoke($controller) {
        $vars = array();
        foreach($this->_children as $child) {
            $vars[] = $child($controller);
        }
        return QTIVariable::sum($vars);
    }
}