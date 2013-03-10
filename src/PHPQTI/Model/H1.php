<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;
use PHPQTI\Model\Base\BlockStatic;
use PHPQTI\Model\Base\AtomicBlock;

class H1 extends \PHPQTI\Model\Gen\H1 implements AtomicBlock, BlockStatic, BodyElement, FlowStatic {

    protected $_elementName = 'h1';

    
}