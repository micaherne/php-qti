<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class GapText extends AbstractClass {

    protected $_elementName = 'gapText';

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