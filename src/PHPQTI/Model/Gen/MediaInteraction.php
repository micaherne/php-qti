<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class MediaInteraction extends AbstractClass {

    protected $_elementName = 'mediaInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $autostart;
    public $minPlays;
    public $maxPlays;
    public $loop;

}