<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class InlineChoiceInteraction extends AbstractClass {

    protected $_elementName = 'inlineChoiceInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $shuffle;
    public $required;

}