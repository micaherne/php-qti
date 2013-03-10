<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class AreaMapEntry extends AbstractClass {

    protected $_elementName = 'areaMapEntry';

    public $shape;
    public $coords;
    public $mappedValue;

}