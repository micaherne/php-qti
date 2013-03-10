<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Hotspot;

use PHPQTI\Model\Base\AssociableChoice;

class AssociableHotspot extends \PHPQTI\Model\Gen\AssociableHotspot 
    implements Hotspot, AssociableChoice {

    protected $_elementName = 'associableHotspot';

    
}