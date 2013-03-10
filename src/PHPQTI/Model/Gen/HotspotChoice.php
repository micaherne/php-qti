<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class HotspotChoice extends AbstractClass {

    protected $_elementName = 'hotspotChoice';

    public $id;
    public $class;
    public $label;
    public $identifier;
    public $fixed;
    public $templateIdentifier;
    public $showHide;
    public $shape;
    public $coords;
    public $hotspotLabel;

}