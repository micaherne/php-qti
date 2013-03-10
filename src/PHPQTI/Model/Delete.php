<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Delete extends \PHPQTI\Model\Gen\Delete implements Expression {

    protected $_elementName = 'delete';

    public function __invoke($controller) {
        $var1 = $this->_children[0]($controller);
        $var2 = $this->_children[1]($controller);
        return $var1->delete($var2);
    }
}