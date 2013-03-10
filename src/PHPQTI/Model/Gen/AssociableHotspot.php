<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class AssociableHotspot extends AbstractClass {

    protected $_elementName = 'associableHotspot';

    public $id;
    public $class;
    public $label;
    public $identifier;
    public $fixed;
    public $templateIdentifier;
    public $showHide;
    public $matchGroup;
    public $shape;
    public $coords;
    public $hotspotLabel;
    public $matchMax;
    public $matchMin;

}