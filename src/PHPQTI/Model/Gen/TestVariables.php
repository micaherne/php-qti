<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class TestVariables extends AbstractClass {

    protected $_elementName = 'testVariables';

    public $sectionIdentifier;
    public $includeCategory;
    public $excludeCategory;
    public $variableIdentifier;
    public $weightIdentifier;
    public $baseType;

}