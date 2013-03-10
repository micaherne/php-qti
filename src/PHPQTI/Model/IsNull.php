<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class IsNull extends \PHPQTI\Model\Gen\IsNull implements Expression {

    protected $_elementName = 'isNull';

    public function __invoke($controller) {
        $what = $this->_children[0]($controller);
        return $what->isNull();
    }
}