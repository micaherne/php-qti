<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\TemplateRule;
use PHPQTI\Runtime\Exception\TemplateConstraintException;

class TemplateConstraint extends \PHPQTI\Model\Gen\TemplateConstraint implements TemplateRule {

    protected $_elementName = 'templateConstraint';

    public function __invoke($controller) {
        foreach($this->_children as $child) {
            $result = $child($controller);
            if ($result->value === false) {
                throw new TemplateConstraintException();
            }
        }
    }
}