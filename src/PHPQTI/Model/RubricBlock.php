<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\InlineStatic;
use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;

class RubricBlock extends \PHPQTI\Model\Gen\RubricBlock implements BodyElement, FlowStatic, InlineStatic {

    protected $_elementName = 'rubricBlock';

    
}