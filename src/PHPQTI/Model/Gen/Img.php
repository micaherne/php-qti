<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class Img extends AbstractClass {

    protected $_elementName = 'img';

    public $id;
    public $class;
    public $label;
    public $src;
    public $alt;
    public $longdesc;
    public $height;
    public $width;

}