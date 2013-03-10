<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;
use PHPQTI\Model\Base\BlockStatic;
use PHPQTI\Model\Base\AtomicBlock;
 
class H5 extends \PHPQTI\Model\Gen\H5 implements AtomicBlock, BlockStatic, BodyElement, FlowStatic {

    protected $_elementName = 'h5';

    
}