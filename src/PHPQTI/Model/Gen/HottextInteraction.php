<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class HottextInteraction extends AbstractClass {

    protected $_elementName = 'hottextInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $maxChoices;
    public $minChoices;

}