<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Inline;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\InlineInteraction;

class InlineChoiceInteraction extends \PHPQTI\Model\Gen\InlineChoiceInteraction
    implements InlineInteraction, Flow, Inline, Interaction {

    protected $_elementName = 'inlineChoiceInteraction';

    
}