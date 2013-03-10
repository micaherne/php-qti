<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class AreaMapping extends AbstractClass {

    protected $_elementName = 'areaMapping';

    public $lowerBound;
    public $upperBound;
    public $defaultValue;

}