<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class Table extends AbstractClass {

    protected $_elementName = 'table';

    public $id;
    public $class;
    public $label;
    public $summary;

}