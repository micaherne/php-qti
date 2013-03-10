<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\FlowStatic;

use PHPQTI\Model\Base\BodyElement;

use PHPQTI\Model\Base\BlockStatic;

class Table extends \PHPQTI\Model\Gen\Table implements BlockStatic, BodyElement, FlowStatic {

    protected $_elementName = 'table';

    
}