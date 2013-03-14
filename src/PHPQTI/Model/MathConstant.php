<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;
use PHPQTI\Runtime\QTIVariable;

class MathConstant extends \PHPQTI\Model\Gen\MathConstant implements Expression {

    protected $_elementName = 'mathConstant';

    public function __invoke($controller) {
        $name = $this->name;
        $result = new QTIVariable('single', 'float');
        $result->setValue(QTIVariable::mathConstant($name));
        return $result;
    }
}