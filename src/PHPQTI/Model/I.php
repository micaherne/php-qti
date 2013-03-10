<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\InlineStatic;
use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;
use PHPQTI\Model\Base\SimpleInline;

class I extends \PHPQTI\Model\Gen\I implements SimpleInline, BodyElement, FlowStatic, InlineStatic {

    protected $_elementName = 'i';

    
}