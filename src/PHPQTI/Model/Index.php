<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Index extends \PHPQTI\Model\Gen\Index implements Expression {

    protected $_elementName = 'index';

    public function __invoke($controller) {
        $n = $controller->valueOrVariable($this->n);
        $what = $this->_children[0]($controller);
        return $what->index($n);
    }
}