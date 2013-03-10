<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\StringInteraction;
use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\Block;
use PHPQTI\Model\Base\BlockInteraction;

class ExtendedTextInteraction extends \PHPQTI\Model\Gen\ExtendedTextInteraction 
    implements StringInteraction, BlockInteraction, Block, Flow, Interaction {

    protected $_elementName = 'extendedTextInteraction';

    
}