<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;
use PHPQTI\Model\Base\BlockStatic;
use PHPQTI\Model\Base\AtomicBlock;
 
class Pre extends \PHPQTI\Model\Gen\Pre implements AtomicBlock, BlockStatic, BodyElement, FlowStatic {

    protected $_elementName = 'pre';

    
}