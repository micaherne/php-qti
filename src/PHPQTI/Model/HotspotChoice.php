<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Hotspot;

use PHPQTI\Model\Base\Choice;

class HotspotChoice extends \PHPQTI\Model\Gen\HotspotChoice implements Hotspot, Choice {

    protected $_elementName = 'hotspotChoice';

    
}