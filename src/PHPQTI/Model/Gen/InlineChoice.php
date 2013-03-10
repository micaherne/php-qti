<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class InlineChoice extends AbstractClass {

    protected $_elementName = 'inlineChoice';

    public $id;
    public $class;
    public $label;
    public $identifier;
    public $fixed;
    public $templateIdentifier;
    public $showHide;

}