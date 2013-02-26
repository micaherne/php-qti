<?php

namespace PHPQTI\Runtime\Element;

use PHPQTI\Runtime\Util\ChoiceIterator;

class ChoiceInteraction extends Element {

    /* 
     * This control will work without Javascript, except for the maxChoices and
     * minChoices. Orientation is not implemented apart from being added to the 
     * CSS classes and JS data attributes for use by clients
     */
    public $simpleChoice = array();
    public $fixed = array(); // indices of simpleChoices with fixed set to true
    public $prompt;

    public function __invoke($controller) {
        $variableName = $this->attrs['responseIdentifier'];
        // Add orientation (this is not used by any other code in the library, 
        // but allows clients to use it if useful)
        if(isset($this->attrs['orientation'])) {
        	$orientationClass = 'qti_orientation_' . $orientation;
        } else {
        	$orientationClass = '';
        }
        $result = "<div id=\"choiceInteraction_{$variableName}\" class=\"qti_blockInteraction {$this->cssClass()} $orientationClass\"";
        $result .= implode(' ', $this->_getDataAttributes(array('maxChoices', 'minChoices', 'orientation')));
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