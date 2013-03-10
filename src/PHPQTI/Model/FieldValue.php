<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class FieldValue extends \PHPQTI\Model\Gen\FieldValue implements Expression {

    protected $_elementName = 'fieldValue';

    public function __invoke($controller) {
        $what = $this->_children[0]($controller);
        return $what->fieldValue($this->fieldIdentifier);
    }
}