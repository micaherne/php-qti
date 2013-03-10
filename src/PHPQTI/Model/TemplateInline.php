<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\FlowStatic;

use PHPQTI\Model\Base\BlockStatic;

use PHPQTI\Model\Base\TemplateElement;

class TemplateInline extends \PHPQTI\Model\Gen\TemplateInline implements 
    BlockStatic, FlowStatic, TemplateElement {

    protected $_elementName = 'templateInline';

    
}