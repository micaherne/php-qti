<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Truncate extends \PHPQTI\Model\Gen\Truncate implements Expression {

    protected $_elementName = 'truncate';

    public function __invoke($controller) {
        $val1 = $this->_children[0]($controller);
    
        return $val1->truncate();
    }
}