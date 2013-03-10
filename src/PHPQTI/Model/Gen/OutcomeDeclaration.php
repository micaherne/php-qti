<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class OutcomeDeclaration extends AbstractClass {

    protected $_elementName = 'outcomeDeclaration';

    public $identifier;
    public $cardinality;
    public $baseType;
    public $view;
    public $interpretation;
    public $longInterpretation;
    public $normalMaximum;
    public $normalMinimum;
    public $masteryValue;

}