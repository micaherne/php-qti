<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class GraphicAssociateInteraction extends AbstractClass {

    protected $_elementName = 'graphicAssociateInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $maxAssociations;
    public $minAssociations;

}