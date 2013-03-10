<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\VariableDeclaration;
use PHPQTI\Runtime\QTIVariable;

class OutcomeDeclaration extends \PHPQTI\Model\Gen\OutcomeDeclaration 
    implements VariableDeclaration {

    protected $_elementName = 'outcomeDeclaration';

    public function __invoke($controller) {
        $variable = QTIVariable::fromDeclaration($this);
        $controller->outcome[$this->identifier] = $variable;
    }
    
}