<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;
use PHPQTI\Runtime\QTIVariable;

class RandomInteger extends \PHPQTI\Model\Gen\RandomInteger implements Expression {

    protected $_elementName = 'randomInteger';

    public function __invoke($controller) {
        $min = $controller->valueOrVariable($this->min);
        $max = $controller->valueOrVariable($this->max);
        $step = isset($this->step) ? $controller->valueOrVariable($this->step) : 1;
    
        $offsetmax = intval($max/$step);
        $value = $min + mt_rand(0, $offsetmax - $min);
        return new QTIVariable('single', 'integer', array(
                'value' => $value
        ));
    }
}