<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class CustomInteraction extends AbstractClass {

    protected $_elementName = 'customInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;

}