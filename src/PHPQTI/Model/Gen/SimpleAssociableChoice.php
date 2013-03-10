<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class SimpleAssociableChoice extends AbstractClass {

    protected $_elementName = 'simpleAssociableChoice';

    public $id;
    public $class;
    public $label;
    public $identifier;
    public $fixed;
    public $templateIdentifier;
    public $showHide;
    public $matchGroup;
    public $matchMax;
    public $matchMin;

}