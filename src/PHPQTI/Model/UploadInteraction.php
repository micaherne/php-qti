<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\Block;
use PHPQTI\Model\Base\BlockInteraction;
 
class UploadInteraction extends \PHPQTI\Model\Gen\UploadInteraction 
    implements BlockInteraction, Block, Flow, Interaction {

    protected $_elementName = 'uploadInteraction';

    public function __invoke($controller) {
    	$variableName = $this->responseIdentifier;
    	$result = "<div id=\"uploadInteraction_{$variableName}\" method=\"post\">";
    	$result .= "<input type=\"file\" name=\"{$variableName}\" >";
    	foreach($this->_children as $child) {
    		if ($child instanceof Prompt) {
    			$result .= "<div class=\"qti_prompt\">" . $child->__invoke($controller) . "</div>";
    		}
    	}
    	$result .= "</div>";
    	return $result;
    }
}