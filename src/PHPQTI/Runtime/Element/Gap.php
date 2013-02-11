<?php

namespace PHPQTI\Runtime\Element;

class Gap extends Element {

    public function __invoke($controller) {
        $gapMatchInteraction = $controller->context['gapMatchInteraction'];
        $identifier = $this->attrs['identifier'];
        $result = "<span class=\"qti_gap\"><select name=\"{$gapMatchInteraction->attrs['responseIdentifier']}[{$identifier}]\">";
        $result .= "<option></option>";
        foreach($gapMatchInteraction->gapChoice as $choice) {
            $variable = $controller->response[$gapMatchInteraction->attrs['responseIdentifier']];
            $directedPairString = $choice->attrs['identifier'] . ' ' . $identifier;

            // Select correct options if we already have a value (i.e. after end attempt)
            if (!empty($variable->value)) {
                if ($variable->cardinality == 'single') {
                    $selected = ($variable->value ==  $directedPairString ? ' selected="selected"' : '');
                } else if ($variable->cardinality == 'multiple') {
                    $selected = (in_array($directedPairString, $variable->value) ? ' selected="selected"' : '');
                }
            } else {
                $selected = '';
            }

            $result .= "<option value=\"{$choice->attrs['identifier']}\" $selected>";
            foreach($choice->children as $child) {
                $result .= $child->__invoke($controller);
            }
            $result .= "</option>";
        }
        $result .= '</select></span>';
        return $result;
    }

}