<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class TemplateDeclaration extends AbstractClass {

    protected $_elementName = 'templateDeclaration';

    public $identifier;
    public $cardinality;
    public $baseType;
    public $paramVariable;
    public $mathVariable;

}