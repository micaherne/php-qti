<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\TemplateRule;

class SetCorrectResponse extends \PHPQTI\Model\Gen\SetCorrectResponse 
    implements TemplateRule {

    protected $_elementName = 'setCorrectResponse';

    public function __invoke($controller) {
        $varname = $this->identifier;
        $controller->response[$varname]->setCorrectResponse($this->_children[0]($controller));
    }
}