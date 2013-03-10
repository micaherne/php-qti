<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\VariableDeclaration;
use PHPQTI\Runtime\QTIVariable;

class ResponseDeclaration extends \PHPQTI\Model\Gen\ResponseDeclaration 
    implements VariableDeclaration {

    protected $_elementName = 'responseDeclaration';
    
    public function __invoke($controller) {
        $variable = QTIVariable::fromDeclaration($this);
        $controller->response[$this->identifier] = $variable;
    }

    
}