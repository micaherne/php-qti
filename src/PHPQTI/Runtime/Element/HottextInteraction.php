<?php

namespace PHPQTI\Runtime\Element;

class HottextInteraction extends Element {

    /* hottextInteraction doesn't implement the shuffle attribute, even though
     * the hottext elements are choices (and therefore theoretically support fixed)
    */
    public $hottext = array();
    public $fixed = array(); // indices of hottexts with fixed set to true
    public $prompt;

    public function __invoke($controller) {
        $variableName = $this->attrs['responseIdentifier'];
        $result = "<div id=\"hottextInteraction_{$variableName}\" class=\"qti_blockInteraction\">";

        // Work out what kind of HTML tag will be used for hottexts
        if (!isset($controller->response[$variableName])) {
            throw new Exception("Declaration for $variableName not found");
        }

        $responseVariable = $controller->response[$variableName];
        $this->hottextType = 'radio';
        $this->brackets = ''; // we need brackets for multiple responses
        if ($responseVariable->cardinality == 'multiple') {
            $this->hottextType = 'checkbox';
            $this->brackets = '[]';
        }

        $this->variableName = $variableName; // to be used by embedded hottext elements

        $controller->context['hottextInteraction'] = $this;
        $this->displayNodes = array();
        // Process child nodes just to find hottexts
        foreach($this->children as $child) {
            if ($child instanceof Prompt) {
                $this->prompt = $child;
            } else {
                $this->displayNodes[] = $child;
            }
        }

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