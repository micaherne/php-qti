<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\GraphicInteraction;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\Block;
use PHPQTI\Model\Base\BlockInteraction;
 
class GraphicGapMatchInteraction extends \PHPQTI\Model\Gen\GraphicGapMatchInteraction 
    implements BlockInteraction, Block, Flow, GraphicInteraction {

    protected $_elementName = 'graphicGapMatchInteraction';

    
}