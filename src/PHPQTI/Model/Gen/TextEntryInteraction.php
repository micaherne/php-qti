<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class TextEntryInteraction extends AbstractClass {

    protected $_elementName = 'textEntryInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $base;
    public $stringIdentifier;
    public $expectedLength;
    public $patternMask;
    public $placeholderText;

}