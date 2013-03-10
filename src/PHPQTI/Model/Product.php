<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Product extends \PHPQTI\Model\Gen\Product implements Expression {

    protected $_elementName = 'product';

    public function __invoke($controller) {
        $vars = array();
        foreach($this->_children as $child) {
            $vars[] = $child($controller);
        }
        return QTIVariable::product($vars);
    }
}