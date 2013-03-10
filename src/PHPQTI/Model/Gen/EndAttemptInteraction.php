<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class EndAttemptInteraction extends AbstractClass {

    protected $_elementName = 'endAttemptInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $title;
    public $countAttempt;

}