<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\TemplateRule;

class TemplateCondition extends \PHPQTI\Model\Gen\TemplateCondition 
    implements TemplateRule {

    protected $_elementName = 'templateCondition';

    public function __invoke($controller) {
        foreach($this->_children as $child) {
            $result = $child($controller);
            if (isset($result->value) && $result->value === true) {
                return;
            }
        }
    }
}