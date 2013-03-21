<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;
use PHPQTI\Runtime\QTIVariable;

class RandomFloat extends \PHPQTI\Model\Gen\RandomFloat implements Expression {

    protected $_elementName = 'randomFloat';

    public function __invoke($controller) {
        $min = $controller->valueOrVariable($this->min);
        $max = $controller->valueOrVariable($this->max);
        
        $value = $randomfloat = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        return new QTIVariable('single', 'float', array(
                'value' => $value
        ));
    }
}