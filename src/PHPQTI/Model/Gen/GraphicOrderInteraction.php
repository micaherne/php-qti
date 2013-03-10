<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class GraphicOrderInteraction extends AbstractClass {

    protected $_elementName = 'graphicOrderInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $minChoices;
    public $maxChoices;

}