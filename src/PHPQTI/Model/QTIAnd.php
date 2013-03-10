<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class QTIAnd extends \PHPQTI\Model\Gen\QTIAnd implements Expression {

    protected $_elementName = 'and';

    public function __invoke($controller) {
        $vars = array();
        foreach($this->_children as $child) {
            $vars[] = $child($controller);
        }
        return QTIVariable::and_($vars);
    }
}