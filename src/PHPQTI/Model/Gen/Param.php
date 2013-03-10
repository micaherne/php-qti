<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class Param extends AbstractClass {

    protected $_elementName = 'param';

    public $name;
    public $value;
    public $valuetype;
    public $type;

}