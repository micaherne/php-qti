<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\VariableDeclaration;
use PHPQTI\Runtime\QTIVariable;

class TemplateDeclaration extends \PHPQTI\Model\Gen\TemplateDeclaration 
    implements VariableDeclaration {

    protected $_elementName = 'templateDeclaration';

    public function __invoke($controller) {
        $variable = QTIVariable::fromDeclaration($this);
        $controller->template[$this->identifier] = $variable;
    }
    
}