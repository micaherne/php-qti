<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Inline;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\InlineInteraction;

class EndAttemptInteraction extends \PHPQTI\Model\Gen\EndAttemptInteraction 
    implements InlineInteraction, Flow, Inline, Interaction {

    protected $_elementName = 'endAttemptInteraction';

    
}