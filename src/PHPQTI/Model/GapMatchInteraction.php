<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\Block;
use PHPQTI\Model\Base\BlockInteraction;

class GapMatchInteraction extends \PHPQTI\Model\Gen\GapMatchInteraction
    implements BlockInteraction, Block, Flow, Interaction {

    protected $_elementName = 'gapMatchInteraction';

    
}