<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class Object extends AbstractClass {

    protected $_elementName = 'object';

    public $id;
    public $class;
    public $label;
    public $data;
    public $type;
    public $width;
    public $height;

}