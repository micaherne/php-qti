<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;
use PHPQTI\Model\Base\BlockStatic;
use PHPQTI\Model\Base\AtomicBlock;

class Address extends \PHPQTI\Model\Gen\Address implements AtomicBlock, BlockStatic, BodyElement, FlowStatic {

    protected $_elementName = 'address';

    
}