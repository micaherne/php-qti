<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class Variable extends AbstractClass {

    protected $_elementName = 'variable';

    public $identifier;
    public $weightIdentifier;

}