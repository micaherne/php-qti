<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class GapMatchInteraction extends AbstractClass {

    protected $_elementName = 'gapMatchInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $shuffle;

}