<?php

namespace PHPQTI\Runtime\Element;

class SelectPointInteraction extends GraphicInteraction {

    public $displayNodes;
    public $prompt;

    public function __invoke($controller) {
        $variableName = $this->attrs['responseIdentifier'];
        $result = "<div id=\"selectPointInteraction_{$variableName}\" class=\"qti_blockInteraction qti_selectPointInteraction\" ";
        $result .= implode(' ', $this->_getDataAttributes(array('maxChoices', 'minChoices')));
        $result .= ">";

        // Work out what kind of HTML tag will be used for simpleMatchSets
        if (!isset($controller->response[$variableName])) {
            throw new Exception("Declaration for $variableName not found");
        }

        $responseVariable = $controller->response[$variableName];

        // Create the hidden input for the variable data
        $result .= "<input type=\"hidden\" name=\"{$variableName}\" value=\"{$responseVariable->valueAsString()}\" />";

        // Process child nodes
        // TODO: We don't really need displayNodes here - it's just
        // used so we don't need to create a qti_object class
        $this->displayNodes = array();
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