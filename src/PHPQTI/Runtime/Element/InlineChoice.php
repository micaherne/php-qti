<?php

namespace PHPQTI\Runtime\Element;

class InlineChoice extends Element {

    public function __invoke($controller) {
        $identifier = $this->attrs['identifier'];

        // See if this response was selected already

        if ($controller->response[$this->name]->value == $identifier) {
            $selected = ' selected="selected" ';
        } else {
            $selected = '';
        }
        $result = '<option value="' . $identifier . "\" $selected>";
        foreach($this->children as $child) { // should be only one
            $result .= $child->__invoke($controller);
        }
        $result .= '</option>';
        return $result;
    }

}