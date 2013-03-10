<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class Selection extends AbstractClass {

    protected $_elementName = 'selection';

    public $select;
    public $withReplacement;

}