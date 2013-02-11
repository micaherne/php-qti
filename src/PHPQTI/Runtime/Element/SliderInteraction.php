<?php

namespace PHPQTI\Runtime\Element;

// TODO: Show min and max labels at either end. Support stepLabel and reverse
class SliderInteraction extends Element {

    public function __invoke($controller) {
        $variableName = $this->attrs['responseIdentifier'];
        $value = $controller->response[$variableName]->getValue();

        $result = '';

        foreach($this->children as $child) {
            if ($child instanceof qti_prompt) {
                $result .= $child->__invoke($controller);
            }
        }

        $result .= "<div class=\"qti_sliderInteraction\"";
        $result .= implode(' ', $this->_getDataAttributes());
        $result .= "> <div class=\"value\"></div> <div class=\"slider\" /></div> ";
        $result .= "<input type=\"hidden\" name=\"{$variableName}\" value=\"{$value}\" />";
        $result .= "</div>";
        return $result;
    }

}