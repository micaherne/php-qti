<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class TemplateBlock extends AbstractClass {

    protected $_elementName = 'templateBlock';

    public $id;
    public $class;
    public $label;
    public $templateIdentifier;
    public $showHide;
    public $identifier;

}