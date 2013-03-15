<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;
use PHPQTI\Runtime\QTIVariable;
use PHPQTI\Runtime\Exception\ProcessingException;

class BaseValue extends \PHPQTI\Model\Gen\BaseValue implements Expression {

    protected $_elementName = 'baseValue';

    public function __invoke($controller) {
        $value = $this->_children[0]($controller);
        if ($this->baseType === 'boolean') {
            if ($value === 'true') {
                $value = true;
            } else if ($value === 'false') {
                $value = false;
            } else {
                throw new ProcessingException("Invalid boolean value");
            }
        }
        return new QTIVariable('single', $this->baseType, array(
                'value' => $value
        ));
    }
}