<?php

namespace PHPQTI\Runtime\Element;

class MatchInteraction extends Element {

    public $simpleMatchSet = array();
    public $prompt;

    public function __invoke($controller) {
        $variableName = $this->attrs['responseIdentifier'];
        $result = "<div id=\"matchInteraction_{$variableName}\" class=\"qti_blockInteraction\">";

        // Work out what kind of HTML tag will be used for simpleMatchSets
        if (!isset($controller->response[$variableName])) {
            throw new Exception("Declaration for $variableName not found");
        }

        $responseVariable = $controller->response[$variableName];

        $this->simpleMatchSet = array();
        $this->fixed = array();
        // Process child nodes
        foreach($this->children as $child) {
            if ($child instanceof Prompt) {
                $this->prompt = $child;
            } else if ($child instanceof SimpleMatchSet) {
                $child->name = $variableName;
                $this->simpleMatchSet[] = $child;
            }
        }

        if (!is_null($this->prompt)) {
            $result .= $this->prompt->__invoke($controller);
        }

        $shuffle = ($this->attrs['shuffle'] == 'true');
        $sourceChoicesIterator = $this->simpleMatchSet[0]->iterator($shuffle);
        $targetChoicesIterator = $this->simpleMatchSet[1]->iterator($shuffle);

        $result .= "<table>";

        // Create headers and extract target identifiers
        $result .= "<tr><td></td>";
        $targetIdentifiers = array();
        foreach($targetChoicesIterator as $targetChoice) {
            $targetIdentifiers[] = $targetChoice->attrs['identifier'];
            $result .= "<td>" . $targetChoice->__invoke($controller) . "</td>";
        }
        $result .= "</tr>";

        foreach($sourceChoicesIterator as $sourceChoice) {
            $result .= "<tr><td>";
            $result .= $sourceChoice->__invoke($controller);
            $result .= "</td>";

            $sourceIdentifier = $sourceChoice->attrs['identifier'];
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