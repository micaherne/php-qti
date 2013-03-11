<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;
use PHPQTI\Runtime\QTIVariable;

class AnyN extends \PHPQTI\Model\Gen\AnyN implements Expression {

    protected $_elementName = 'anyN';

    public function __invoke($controller) {
        $vars = array();
        foreach($this->_children as $child) {
            $vars[] = $child($controller);
        }
        $min = $this->valueOrVariable($this->min);
        $max = $this->valueOrVariable($this->max);
        return QTIVariable::anyN($min, $max, $vars);
    }
}