<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\Block;
use PHPQTI\Model\Base\BlockInteraction;

class OrderInteraction extends \PHPQTI\Model\Gen\OrderInteraction 
    implements BlockInteraction, Block, Flow, Interaction {

    protected $_elementName = 'orderInteraction';

    
}