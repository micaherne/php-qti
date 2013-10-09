<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\Block;
use PHPQTI\Model\Base\BlockInteraction;

class HottextInteraction extends \PHPQTI\Model\Gen\HottextInteraction 
    implements BlockInteraction, Block, Flow, Interaction {

    protected $_elementName = 'hottextInteraction';
    
    public function __invoke($controller) {
        $result = "<div id=\"hottextInteraction_{$this->responseIdentifier}\" class=\"qti_blockInteraction\">";
    
        // Work out what kind of HTML tag will be used for hottexts
        if (!isset($controller->response[$this->responseIdentifier])) {
            throw new Exception("Declaration for $this->responseIdentifier not found");
        }
    
        $responseVariable = $controller->response[$this->responseIdentifier];
        
        $displayNodes = array();
        $prompt = null;
        // Process child nodes just to find hottexts
        foreach($this->_children as $child) {
            if ($child instanceof Prompt) {
                $prompt = $child;
            } else {
                $displayNodes[] = $child;
            }
        }
    
        if (!is_null($prompt)) {
            $result .= $prompt($controller);
        }
    
        foreach($displayNodes as $node) {
            $result .= $node($controller);
        }
    
        $result .= "</div>";
        return $result;
    }

    
}