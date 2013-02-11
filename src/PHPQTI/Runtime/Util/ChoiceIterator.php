<?php

namespace PHPQTI\Runtime\Util;

/**
 * An iterator which will iterate over an array of choices, taking into
 * account the shuffle and fixed attributes.
 */
class ChoiceIterator implements Iterator {

    protected $choices;
    protected $position = 0;

    public function __construct($choiceArray, $shuffle = false) {
        $this->position = 0;

        $identifiers = array();
        $fixed = array();
        for($i = 0; $i < count($choiceArray); $i++) {
            if (isset($choiceArray[$i]->attrs['fixed']) && $choiceArray[$i]->attrs['fixed'] == 'true') {
                $fixed[] = $i;
            }
        }
        $order = range(0, count($choiceArray) - 1);
        if ($shuffle) {
            $notfixed = array_diff($order, $fixed);
            shuffle($notfixed);
            $shuffledused = 0;
            for($i = 0; $i < count($choiceArray); $i++) {
                if(in_array($i, $fixed)) {
                    $this->choices[] = $choiceArray[$i];
                } else {
                    $this->choices[] = $choiceArray[$notfixed[$shuffledused]];
                    $shuffledused++;
                }
            }
        } else {
            $this->choices = $choiceArray;
        }

    }

    public function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->choices[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->choices[$this->position]);
    }
}