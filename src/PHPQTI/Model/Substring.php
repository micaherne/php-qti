<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Substring extends \PHPQTI\Model\Gen\Substring implements Expression {

    protected $_elementName = 'substring';

    public function __invoke($controller) {
        $var1 = $this->_children[0]($controller);
        $var2 = $this->_children[1]($controller);
        return $var1->substring($var2, $this->caseSensitive);
    }
}