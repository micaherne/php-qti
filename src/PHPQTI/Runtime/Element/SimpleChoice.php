<?php

namespace PHPQTI\Runtime\Element;

class SimpleChoice extends Element {

    public $interactionType = 'choiceInteraction';

    public function __invoke($controller) {
        $result = "<span class=\"qti_simpleChoice\">\n";
        if ($this->interactionType == 'choiceInteraction') {

            // str_replace is for checkboxes where the element name always has [] at the end
            $responseValue = $controller->response[str_replace('[]', '', $this->name)]->value;

            // See if this response was selected already
            // TODO: Do this checking in qti_variable so it can be reused
            if (is_array($responseValue)) {
                $checked = in_array($this->attrs['identifier'], $responseValue) ? ' checked="checked"' : '';
            } else {
                $checked = $responseValue == $this->attrs['identifier'] ? ' checked="checked"' : '';
            }
            $result .= "<input type=\"{$this->inputType}\" name=\"{$this->name}\" value=\"{$this->attrs['identifier']}\" $checked></input>\n";
        } else if ($this->interactionType = 'orderInteraction') {
            $result .= "<select name=\"{$this->name}[{$this->attrs['identifier']}]\">\n";
            $result .= "<option></option>";
            for($i = 1; $i <= $this->numberOfChoices; $i++) {
                $selected = $controller->response[$this->name]->value[$i - 1] == $this->attrs['identifier'] ? ' selected="selected"' : '';
                $result .= "<option value=\"$i\" $selected>$i</option>";
            }
            $result .= "</select>";
        }
        foreach($this->children as $child) {
            $result .= $child($controller);
        }
        $result .= "</span>";
        return $result;
    }

}