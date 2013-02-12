<?php

namespace PHPQTI\Runtime\Element;

use PHPQTI\Runtime\Util\ChoiceIterator;

class ChoiceInteraction extends Element {

    /* TODO: We'd really like to tell the simpleChoice elements what type of
     * input control they're to display in the constructor, but we don't have access to the
    * variable declarations.
    */

    public $simpleChoice = array();
    public $fixed = array(); // indices of simpleChoices with fixed set to true
    public $prompt;

    public function __invoke($controller) {
        $variableName = $this->attrs['responseIdentifier'];
        $result = "<div id=\"choiceInteraction_{$variableName}\" class=\"qti_blockInteraction\"";
        $result .= implode(' ', $this->_getDataAttributes(array('maxChoices', 'minChoices')));
        $result .= ">";
        // Work out what kind of HTML tag will be used for simpleChoices
        if (!isset($controller->response[$variableName])) {
            throw new \Exception("Declaration for $variableName not found");
        }

        $responseVariable = $controller->response[$variableName];
        $simpleChoiceType = 'radio';
        $brackets = ''; // we need brackets for multiple responses
        if ($responseVariable->cardinality == 'multiple') {
            $simpleChoiceType = 'checkbox';
            $brackets = '[]';
        }

        $this->simpleChoice = array();
        $this->fixed = array();
        // Process child nodes
        foreach($this->children as $child) {
            if ($child instanceof Prompt) {
                $this->prompt = $child;
            } else if ($child instanceof SimpleChoice) {
                $child->inputType = $simpleChoiceType;
                $child->name = $variableName.$brackets;
                $this->simpleChoice[] = $child;
                if(isset($child->attrs['fixed']) && $child->attrs['fixed'] === 'true') {
                    $this->fixed[] = count($this->simpleChoice) - 1;
                }
            }
        }

        if (!is_null($this->prompt)) {
            $result .= $this->prompt->__invoke($controller);
        }

        $shuffle = $this->attrs['shuffle'] === 'true';
        $choiceIterator = new ChoiceIterator($this->simpleChoice, $shuffle);
        foreach($choiceIterator as $choice) {
            $result .= $choice->__invoke($controller);
        }

        $result .= "</div>";
        return $result;
    }

}