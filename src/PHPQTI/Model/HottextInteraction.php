<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\Block;
use PHPQTI\Model\Base\BlockInteraction;

class HottextInteraction extends \PHPQTI\Model\Gen\HottextInteraction 
    implements BlockInteraction, Block, Flow, Interaction {

    protected $_elementName = 'hottextInteraction';

    
}