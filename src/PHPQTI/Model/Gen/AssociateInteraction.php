<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class AssociateInteraction extends AbstractClass {

    protected $_elementName = 'associateInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $shuffle;
    public $maxAssociations;
    public $minAssociations;

}