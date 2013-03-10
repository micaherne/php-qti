<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class Mapping extends AbstractClass {

    protected $_elementName = 'mapping';

    public $lowerBound;
    public $upperBound;
    public $defaultValue;

}