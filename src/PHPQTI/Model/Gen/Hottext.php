<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class Hottext extends AbstractClass {

    protected $_elementName = 'hottext';

    public $id;
    public $class;
    public $label;
    public $identifier;
    public $fixed;
    public $templateIdentifier;
    public $showHide;

}