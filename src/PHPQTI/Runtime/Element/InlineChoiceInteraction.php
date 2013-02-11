<?php

namespace PHPQTI\Runtime\Element;

// TODO: Implement "required" attribute
class InlineChoiceInteraction extends Element {

    public $inlineChoice = array();
    public $fixed = array(); // indices of inlineChoices with fixed set to true

    public function __invoke($controller) {
        $variableName = $this->attrs['responseIdentifier'];
        $result = "<select name=\"{$variableName}\" id=\"inlineChoiceInteraction_{$variableName}\" class=\"qti_inlineChoiceInteraction\">";
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
        foreach($this->children as $child) {
            if ($child instanceof InlineChoice) {
                $this->inlineChoice[] = $child;
                $child->name = $variableName;
                if($child->attrs['fixed'] === 'true') {
                    $this->fixed[] = count($this->inlineChoice) - 1;
                }
            } else {
                throw new Exception("Unknown child element in inlineChoice");
            }
        }

        // Work out an order to display them in
        // TODO: Worst implementation ever!
        $order = range(0, count($this->inlineChoice) - 1);
        if ($this->attrs['shuffle'] === 'true') {
            $notfixed = array_diff($order, $this->fixed);
            shuffle($notfixed);
            $shuffledused = 0;
            for($i = 0; $i < count($this->inlineChoice); $i++) {
                if(in_array($i, $this->fixed)) {
                    $result .= $this->inlineChoice[$i]->__invoke($controller);
                } else {
                    $result .= $this->inlineChoice[$notfixed[$shuffledused++]]->__invoke($controller);
                }
            }
        } else {
            foreach($order as $i) {
                $result .= $this->inlineChoice[$i]->__invoke($controller);
            }
        }

        $result .= "</select>";
        return $result;
    }

}