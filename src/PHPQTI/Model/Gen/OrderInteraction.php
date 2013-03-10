<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class OrderInteraction extends AbstractClass {

    protected $_elementName = 'orderInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $shuffle;
    public $minChoices;
    public $maxChoices;
    public $orientation;

}