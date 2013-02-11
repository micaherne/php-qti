<?php

namespace PHPQTI\Runtime\Element;

// TODO: Implement associableChoice (in particular matchGroup)
class SimpleAssociableChoice extends Element {

    public function __invoke($controller) {
        $result = "";
        foreach($this->children as $child) {
            $result .= $child->__invoke($controller);
        }
        return $result;
    }

}