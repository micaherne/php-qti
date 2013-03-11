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
    
    public function __invoke($controller) {
    	$variableName = $this->responseIdentifier;
    	$value = $controller->response[$variableName]->getValue();
    	$result = "<input type=\"text\" name=\"{$variableName}\" value=\"{$value}\"></input>";
    	return $result;
    }

    
}