<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\TextOrVariable;

use PHPQTI\Model\Base\InlineStatic;

use PHPQTI\Model\Base\FlowStatic;

use PHPQTI\Model\Base\BodyElement;

class PrintedVariable extends \PHPQTI\Model\Gen\PrintedVariable implements BodyElement,
    FlowStatic, InlineStatic, TextOrVariable {

    protected $_elementName = 'printedVariable';

    public function __invoke($controller) {
        $variable = $controller->template[$this->identifier];
        if (is_array($variable->value)) {
        	return implode(', ', $variable->value);
        } else {
        	return $variable->value;
        }
    }
}