<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class RandomInteger extends AbstractClass {

    protected $_elementName = 'randomInteger';

    public $min;
    public $max;
    public $step;

}