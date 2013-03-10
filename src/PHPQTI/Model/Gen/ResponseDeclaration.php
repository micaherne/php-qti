<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class ResponseDeclaration extends AbstractClass {

    protected $_elementName = 'responseDeclaration';

    public $identifier;
    public $cardinality;
    public $baseType;

}