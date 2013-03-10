<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\InlineStatic;
use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;

class Blockquote extends \PHPQTI\Model\Gen\Blockquote implements BodyElement, FlowStatic, InlineStatic {

    protected $_elementName = 'blockquote';

    
}