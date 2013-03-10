<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Ordered extends \PHPQTI\Model\Gen\Ordered implements Expression {

    protected $_elementName = 'ordered';

    public function __invoke($controller) {
        $vars = array();
        foreach($this->_children as $child) {
            $vars[] = $child($controller);
        }
        return QTIVariable::ordered($vars);
    }
}