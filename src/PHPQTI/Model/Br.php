<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\InlineStatic;
use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;
use PHPQTI\Model\Base\AtomicInline;

class Br extends \PHPQTI\Model\Gen\Br implements AtomicInline, BodyElement, FlowStatic, InlineStatic {

    protected $_elementName = 'br';

    
}