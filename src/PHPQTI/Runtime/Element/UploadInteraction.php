<?php

namespace PHPQTI\Runtime\Element;

class UploadInteraction extends Element {

    public function __invoke($controller) {
        $variableName = $this->attrs['responseIdentifier'];
        $result = "<div id=\"uploadInteraction_{$variableName}\" method=\"post\">";
        $result .= "<input type=\"file\" name=\"{$variableName}\" >";
        foreach($this->children as $child) {
            if ($child instanceof Prompt) {
                $result .= "<div class=\"qti_prompt\">" . $child->__invoke($controller) . "</div>";
            }
        }
        $result .= "</div>";
        return $result;
    }

}