<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class UploadInteraction extends AbstractClass {

    protected $_elementName = 'uploadInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $type;

}