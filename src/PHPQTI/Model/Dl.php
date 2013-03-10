<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\FlowStatic;

use PHPQTI\Model\Base\BodyElement;

use PHPQTI\Model\Base\BlockStatic;

class Dl extends \PHPQTI\Model\Gen\Dl implements BlockStatic, BodyElement, FlowStatic {

    protected $_elementName = 'dl';

    
}