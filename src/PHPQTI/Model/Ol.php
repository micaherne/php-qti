<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;
use PHPQTI\Model\Base\BlockStatic;

class Ol extends \PHPQTI\Model\Gen\Ol implements BlockStatic, BodyElement, FlowStatic {

    protected $_elementName = 'ol';

    
}