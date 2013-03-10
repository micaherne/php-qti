<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\StringInteraction;

use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Inline;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\InlineInteraction;
 
class TextEntryInteraction extends \PHPQTI\Model\Gen\TextEntryInteraction 
    implements StringInteraction, InlineInteraction, Flow, Inline, Interaction {

    protected $_elementName = 'textEntryInteraction';

    
}