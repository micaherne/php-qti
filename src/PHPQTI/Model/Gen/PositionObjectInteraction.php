<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class PositionObjectInteraction extends AbstractClass {

    protected $_elementName = 'positionObjectInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $centrePoint;
    public $minChoices;
    public $maxChoices;

}