<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\TemplateRule;

class SetTemplateValue extends \PHPQTI\Model\Gen\SetTemplateValue implements TemplateRule{

    protected $_elementName = 'setTemplateValue';

    public function __invoke($controller) {
        $varname = $this->identifier;
        $controller->template[$varname]->setValue($this->_children[0]($controller));
    }
}