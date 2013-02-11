<?php

namespace PHPQTI\Runtime\Element;

class SimpleMatchSet extends Element {

    public function iterator($shuffle = false) {
        return new ChoiceIterator($this->children, $shuffle);
    }

}