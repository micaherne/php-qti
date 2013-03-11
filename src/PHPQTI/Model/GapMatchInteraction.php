<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\Block;
use PHPQTI\Model\Base\BlockInteraction;

class GapMatchInteraction extends \PHPQTI\Model\Gen\GapMatchInteraction
    implements BlockInteraction, Block, Flow, Interaction {

    protected $_elementName = 'gapMatchInteraction';

    public function __invoke($controller) {
        $variableName = $this->responseIdentifier;
        $result = "<div id=\"gapMatchInteraction_{$variableName}\" class=\"qti_blockInteraction\">";
    
        // Find variable
        if (!isset($controller->response[$variableName])) {
            throw new Exception("Declaration for $variableName not found");
        }
    
        $responseVariable = $controller->response[$variableName];
    
        $this->gapChoice = array();
        // TODO: Implement gapImg
        $this->fixed = array();
        $this->displayNodes = array(); // Nodes which will be processed for display as normal
        // Process child nodes
        foreach($this->_children as $child) {
            if ($child instanceof GapText) {
                $this->gapChoice[] = $child;
                if(isset($child->fixed) && $child->fixed === 'true') {
                    $this->fixed[] = count($this->gapChoice) - 1;
                }
            } else {
                $this->displayNodes[] = $child;
            }
        }
    
        foreach($this->displayNodes as $node) {
            $result .= $node->__invoke($controller);
        }
    
        $result .= "</div>";
        return $result;
    }
}