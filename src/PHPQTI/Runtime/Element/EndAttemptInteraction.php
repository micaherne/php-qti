<?php

namespace PHPQTI\Runtime\Element;

class EndAttemptInteraction extends Element {

    public function __invoke($controller) {
        $variableName = $this->attrs['responseIdentifier'];
        $result = "<div id=\"endAttemptInteraction_{$variableName}\" class=\"qti_endAttemptInteraction\" method=\"post\">";
        $result .= "<input type=\"hidden\" name=\"{$variableName}\" value=\"false\" />";
        $result .= "<input type=\"submit\" value=\"{$this->attrs['title']}\" >";
        $result .= "</div>";
        return $result;
    }

}