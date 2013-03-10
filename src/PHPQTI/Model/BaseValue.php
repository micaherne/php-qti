<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;
use PHPQTI\Runtime\QTIVariable;

class BaseValue extends \PHPQTI\Model\Gen\BaseValue implements Expression {

    protected $_elementName = 'baseValue';

    public function __invoke($controller) {
        return new QTIVariable('single', $this->baseType, array(
                'value' => $this->_children[0]($controller)
        ));
    }
}