<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class MatchInteraction extends AbstractClass {

    protected $_elementName = 'matchInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $shuffle;
    public $maxAssociations;
    public $minAssociations;

}