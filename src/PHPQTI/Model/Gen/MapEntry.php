<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class MapEntry extends AbstractClass {

    protected $_elementName = 'mapEntry';

    public $mapKey;
    public $mappedValue;
    public $caseSensitive;

}