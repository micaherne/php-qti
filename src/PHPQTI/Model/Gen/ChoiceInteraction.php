<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class ChoiceInteraction extends AbstractClass {

    protected $_elementName = 'choiceInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $shuffle;
    public $maxChoices;
    public $minChoices;
    public $orientation;

}