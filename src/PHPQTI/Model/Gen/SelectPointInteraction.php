<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class SelectPointInteraction extends AbstractClass {

    protected $_elementName = 'selectPointInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $minChoices;
    public $maxChoices;

}