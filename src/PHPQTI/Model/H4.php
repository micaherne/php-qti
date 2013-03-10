<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;
use PHPQTI\Model\Base\BlockStatic;
use PHPQTI\Model\Base\AtomicBlock;
 
class H4 extends \PHPQTI\Model\Gen\H4 implements AtomicBlock, BlockStatic, BodyElement, FlowStatic {

    protected $_elementName = 'h4';

    
}