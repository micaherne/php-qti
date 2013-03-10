<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class Stylesheet extends AbstractClass {

    protected $_elementName = 'stylesheet';

    public $href;
    public $type;
    public $media;
    public $title;

}