<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\GraphicInteraction;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\Block;
use PHPQTI\Model\Base\BlockInteraction;

class GraphicOrderInteraction extends \PHPQTI\Model\Gen\GraphicOrderInteraction 
    implements BlockInteraction, Block, Flow, GraphicInteraction {

    protected $_elementName = 'graphicOrderInteraction';

    
}