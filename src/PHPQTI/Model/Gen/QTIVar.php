<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class QTIVar extends AbstractClass {

    protected $_elementName = 'var';

    public $id;
    public $class;
    public $label;

}