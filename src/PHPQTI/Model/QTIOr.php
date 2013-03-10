<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class QTIOr extends \PHPQTI\Model\Gen\QTIOr implements Expression {

    protected $_elementName = 'or';

    public function __invoke($controller) {
        $vars = array();
        foreach($this->_children as $child) {
            $vars[] = $child($controller);
        }
        return QTIVariable::or_($vars);
    }
}