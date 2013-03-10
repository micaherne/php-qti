<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class PatternMatch extends \PHPQTI\Model\Gen\PatternMatch implements Expression {

    protected $_elementName = 'patternMatch';

    public function __invoke($controller) {
        $val1 = $this->_children[0]($controller);

        // Do variable substitution
        $pattern = $controller->stringOrVariable($this->pattern);
        
        return $val1->patternMatch($pattern);
    }
}