<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Random extends \PHPQTI\Model\Gen\Random implements Expression {

    protected $_elementName = 'random';

    public function __invoke($controller) {
        $what = $this->_children[0]($controller);
        return $what->random();
    }
}