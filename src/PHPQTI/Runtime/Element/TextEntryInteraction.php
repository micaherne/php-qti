<?php

namespace PHPQTI\Runtime\Element;

class TextEntryInteraction extends StringInteraction {

    public function __invoke($controller) {
        $variableName = $this->attrs['responseIdentifier'];
        $value = $controller->response[$variableName]->getValue();
        $result = "<input type=\"text\" name=\"{$variableName}\" value=\"{$value}\"></input>";
        return $result;
    }

}