<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class GapImg extends AbstractClass {

    protected $_elementName = 'gapImg';

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
    public $objectLabel;

}