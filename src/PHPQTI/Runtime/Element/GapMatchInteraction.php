<?php

namespace PHPQTI\Runtime\Element;

class GapMatchInteraction extends Element {

    /* TODO: gapMatchInteraction should support shuffle (for the choices, not gaps!)
     */

    public $gapChoice = array();
    public $fixed = array(); // indices of gapChoices with fixed set to true
    public $prompt;

    public function __invoke($controller) {
        $variableName = $this->attrs['responseIdentifier'];
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
        foreach($this->children as $child) {
            if ($child instanceof Prompt) {
                $this->prompt = $child;
            } else if ($child instanceof GapChoice) {
                $this->gapChoice[] = $child;
                if(isset($child->attrs['fixed']) && $child->attrs['fixed'] === 'true') {
                    $this->fixed[] = count($this->gapChoice) - 1;
                }
            } else {
                $this->displayNodes[] = $child;
            }
        }

        $controller->context['gapMatchInteraction'] = $this;

        if (!is_null($this->prompt)) {
            $result .= $this->prompt->__invoke($controller);
        }
        
        foreach($this->displayNodes as $node) {
            $result .= $node->__invoke($controller);
        }

        $result .= "</div>";
        return $result;
    }

}