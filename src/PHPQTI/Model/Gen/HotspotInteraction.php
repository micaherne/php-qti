<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class HotspotInteraction extends AbstractClass {

    protected $_elementName = 'hotspotInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $minChoices;
    public $maxChoices;

}