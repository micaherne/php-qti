<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Round extends \PHPQTI\Model\Gen\Round implements Expression {

    protected $_elementName = 'round';

    public function __invoke($controller) {
        $val1 = $this->_children[0]($controller);
    
        return $val1->round();
    }
}