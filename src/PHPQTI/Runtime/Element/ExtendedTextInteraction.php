<?php

namespace PHPQTI\Runtime\Element;

class ExtendedTextInteraction extends StringInteraction {

    public function __invoke($controller) {
        $variableName = $this->attrs['responseIdentifier'];
        $variable = $controller->response[$variableName];
        $result = '';

        // Process child nodes
        foreach($this->children as $child) {
            if ($child instanceof Prompt) {
                $result .= $child->__invoke($controller);
            }
        }

        if ($variable->cardinality == 'single') {
            $brackets = '';
            $values = array($variable->value);
            $count = 1;
        } else {
            $brackets = '[]';
            $values = $variable->value;
            $count = $this->attrs['maxStrings'];
        }


        for($i = 0; $i < $count; $i++) {
            if(isset($values[$i])) {
                $value = $values[$i];
            } else {
                $value = '';
            }
            $result .= "<textarea name=\"{$variableName}{$brackets}\">" . htmlentities($value) . "</textarea>";
        }
        return $result;
    }

}