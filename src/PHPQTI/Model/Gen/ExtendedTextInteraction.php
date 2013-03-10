<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class ExtendedTextInteraction extends AbstractClass {

    protected $_elementName = 'extendedTextInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $base;
    public $stringIdentifier;
    public $expectedLength;
    public $patternMask;
    public $placeholderText;
    public $maxStrings;
    public $minStrings;
    public $expectedLines;
    public $format;

}