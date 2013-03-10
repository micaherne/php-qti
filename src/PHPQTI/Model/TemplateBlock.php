<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BlockStatic;
use PHPQTI\Model\Base\TemplateElement;

class TemplateBlock extends \PHPQTI\Model\Gen\TemplateBlock implements TemplateElement,
    BlockStatic, FlowStatic {

    protected $_elementName = 'templateBlock';

    
}