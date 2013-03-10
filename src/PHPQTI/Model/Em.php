<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\InlineStatic;
use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;
use PHPQTI\Model\Base\SimpleInline;
 
class Em extends \PHPQTI\Model\Gen\Em implements SimpleInline, BodyElement, FlowStatic, InlineStatic {

    protected $_elementName = 'em';

    
}