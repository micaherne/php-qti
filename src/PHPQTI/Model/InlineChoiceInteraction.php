<?php

namespace PHPQTI\Model;

use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Inline;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\InlineInteraction;
use PHPQTI\Util\ChoiceIterator;

class InlineChoiceInteraction extends \PHPQTI\Model\Gen\InlineChoiceInteraction
implements InlineInteraction, Flow, Inline, Interaction {

    protected $_elementName = 'inlineChoiceInteraction';

    public function __invoke($controller) {
        $variableName = $this->responseIdentifier;
        if (isset($this->required) && $this->required == 'true') {
            $required = ' required = "required" ';
        } else {
            $required = '';
        }
        $result = "<select name=\"{$variableName}\" id=\"inlineChoiceInteraction_{$variableName}\" class=\"qti_inlineChoiceInteraction\" $required>";
        // Empty choice
        $result .= "<option></option>";

        // Find variable
        if (!isset($controller->response[$variableName])) {
            throw new Exception("Declaration for $variableName not found");
        }

        $responseVariable = $controller->response[$variableName];

        $this->inlineChoice = array();
        $this->fixed = array();
        // Process child nodes
        foreach($this->_children as $child) {
            if ($child instanceof InlineChoice) {
                $this->inlineChoice[] = $child;
                $child->name = $variableName;
                if(isset($child->fixed) && $child->fixed === 'true') {
                    $this->fixed[] = count($this->inlineChoice) - 1;
                }
            } else {
                throw new Exception("Unknown child element in inlineChoice");
            }
        }

        $shuffle = $this->shuffle === 'true';
        $choiceIterator = new ChoiceIterator($this->inlineChoice, $shuffle);
        foreach($choiceIterator as $choice) {
            $result .= $choice->__invoke($controller);
        }

        $result .= "</select>";
        return $result;
    }
}