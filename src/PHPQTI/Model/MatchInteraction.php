<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\Block;
use PHPQTI\Model\Base\BlockInteraction;

class MatchInteraction extends \PHPQTI\Model\Gen\MatchInteraction 
    implements BlockInteraction, Block, Flow, Interaction {

    protected $_elementName = 'matchInteraction';

    public function __invoke($controller) {
    	$variableName = $this->responseIdentifier;
    	$result = "<div id=\"matchInteraction_{$variableName}\" class=\"qti_blockInteraction\">";
    
    	// Work out what kind of HTML tag will be used for simpleMatchSets
    	if (!isset($controller->response[$variableName])) {
    		throw new Exception("Declaration for $variableName not found");
    	}
    
    	$responseVariable = $controller->response[$variableName];
    
    	$simpleMatchSet = array();
    	$fixed = array();
    	$prompt = null;
    	// Process child nodes
    	foreach($this->_children as $child) {
    		if ($child instanceof Prompt) {
    			$prompt = $child;
    		} else if ($child instanceof SimpleMatchSet) {
    			$child->name = $variableName;
    			$simpleMatchSet[] = $child;
    		}
    	}
    
    	if (!is_null($prompt)) {
    		$result .= $prompt->__invoke($controller);
    	}
    
    	$shuffle = ($this->shuffle == 'true');
    	$sourceChoicesIterator = $simpleMatchSet[0]->iterator($shuffle);
    	$targetChoicesIterator = $simpleMatchSet[1]->iterator($shuffle);
    
    	$result .= "<table>";
    
    	// Create headers and extract target identifiers
    	$result .= "<tr><td></td>";
    	$targetIdentifiers = array();
    	foreach($targetChoicesIterator as $targetChoice) {
    		$targetIdentifiers[] = $targetChoice->identifier;
    		$result .= "<td>" . $targetChoice->__invoke($controller) . "</td>";
    	}
    	$result .= "</tr>";
    
    	foreach($sourceChoicesIterator as $sourceChoice) {
    		$result .= "<tr><td>";
    		$result .= $sourceChoice($controller);
    		$result .= "</td>";
    
    		$sourceIdentifier = $sourceChoice->identifier;
    		foreach($targetIdentifiers as $targetIdentifier) {
    			$result .= "<td>";
    			// Tick values from variable
    			if (isset($responseVariable->value) && in_array("{$sourceIdentifier} {$targetIdentifier}", $responseVariable->value)) {
    				$checked = " checked=\"checked\" ";
    			} else {
    				$checked = "";
    			}
    			$result .= "<input type=\"checkbox\" name=\"{$variableName}[{$targetIdentifier}][]\" value=\"{$sourceIdentifier}\" $checked/>";
    			$result .= "</td>";
    		}
    
    		$result .= "</tr>";
    	}
    
    	$result .= "</table>";
    	$result .= "</div>";
    	return $result;
    }
    
}