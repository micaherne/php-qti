<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class PrintedVariable extends AbstractClass {

    protected $_elementName = 'printedVariable';

    public $id;
    public $class;
    public $label;
    public $identifier;
    public $format;
    public $base;
    public $index;
    public $powerForm;
    public $field;
    public $delimiter;
    public $mappingIndicator;

}