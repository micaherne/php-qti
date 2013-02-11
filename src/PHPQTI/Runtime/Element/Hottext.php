<?php

namespace PHPQTI\Runtime\Element;

class Hottext extends Element {

    public $interactionType = 'choiceInteraction';

    public function __invoke($controller) {
        $result = "<span class=\"qti_hottext\">\n";

        $identifier = $this->attrs['identifier'];
        $hottextInteraction = $controller->context['hottextInteraction'];

        $variable = $controller->response[$hottextInteraction->variableName];
        $testvar = new qti_variable('single', $variable->type, array('value' => $identifier));
        if ($variable->cardinality == 'multiple') {
            $comparisonresult = $variable->contains($testvar);
        } else {
            $comparisonresult = $variable->match($testvar);
        }
        $checked = $comparisonresult->value ? " checked=\"checked\" " : "";
        $result .= "<input type=\"{$hottextInteraction->hottextType}\" name=\"{$hottextInteraction->variableName}{$hottextInteraction->brackets}\" value=\"{$identifier}\" {$checked}/> ";

        foreach($this->children as $child) {
            $result .= $child($controller);
        }
        $result .= "</span>";
        return $result;
    }

}