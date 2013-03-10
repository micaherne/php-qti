<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class Th extends AbstractClass {

    protected $_elementName = 'th';

    public $id;
    public $class;
    public $label;
    public $headers;
    public $scope;
    public $abbr;
    public $axis;
    public $rowspan;
    public $colspan;
    public $align;
    public $valign;

}