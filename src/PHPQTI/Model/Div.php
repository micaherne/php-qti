<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\FlowStatic;

use PHPQTI\Model\Base\BodyElement;

use PHPQTI\Model\Base\BlockStatic;

class Div extends \PHPQTI\Model\Gen\Div implements BlockStatic, BodyElement, FlowStatic {

    protected $_elementName = 'div';

    
}