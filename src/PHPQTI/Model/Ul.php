<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;
use PHPQTI\Model\Base\BlockStatic;

class Ul extends \PHPQTI\Model\Gen\Ul implements BlockStatic, BodyElement, FlowStatic {

    protected $_elementName = 'ul';

    
}