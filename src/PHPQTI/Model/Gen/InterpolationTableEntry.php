<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class InterpolationTableEntry extends AbstractClass {

    protected $_elementName = 'interpolationTableEntry';

    public $sourceValue;
    public $includeBoundary;
    public $targetValue;

}