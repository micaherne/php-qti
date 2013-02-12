<?php

namespace PHPQTI\Runtime\Element;

class AssociateInteraction extends Element {

    public $simpleAssociableChoice = array();
    public $fixed = array(); // indices of simpleAssociableChoices with fixed set to true
    public $prompt;

    public function __invoke($controller) {
        $variableName = $this->attrs['responseIdentifier'];
        $result = "<div id=\"associateInteraction_{$variableName}\" class=\"qti_blockInteraction\">";

        // Work out what kind of HTML tag will be used for simpleAssociableChoices
        if (!isset($controller->response[$variableName])) {
            throw new Exception("Declaration for $variableName not found");
        }

        $responseVariable = $controller->response[$variableName];

        $this->simpleAssociableChoice = array();
        $this->fixed = array();
        // Process child nodes
        foreach($this->children as $child) {
            if ($child instanceof Prompt) {
                $this->prompt = $child;
            } else if ($child instanceof SimpleAssociableChoice) {
                $child->name = $variableName;
                $this->simpleAssociableChoice[] = $child;
                if(isset($child->attrs['fixed']) && $child->attrs['fixed'] === 'true') {
                    $this->fixed[] = count($this->simpleAssociableChoice) - 1;
                }
            }
        }

        if (!is_null($this->prompt)) {
            $result .= $this->prompt->__invoke($controller);
        }

        $result .= "<ol>";
        // Work out an order to display them in
        // TODO: Worst implementation ever!
        $identifiers = array();
        $order = range(0, count($this->simpleAssociableChoice) - 1);
        if ($this->attrs['shuffle'] === 'true') {
            $notfixed = array_diff($order, $this->fixed);
            shuffle($notfixed);
            $shuffledused = 0;
            for($i = 0; $i < count($this->simpleAssociableChoice); $i++) {
                if(in_array($i, $this->fixed)) {
                    $identifiers[] = $this->simpleAssociableChoice[$i]->attrs['identifier'];
                    $result .= "<li>" . $this->simpleAssociableChoice[$i]->__invoke($controller) . "</li>";
                } else {
                    $identifiers[] = $this->simpleAssociableChoice[$notfixed[$shuffledused]]->attrs['identifier'];
                    $result .= "<li>" . $this->simpleAssociableChoice[$notfixed[$shuffledused++]]->__invoke($controller) . "</li>";
                }
            }
        } else {
            foreach($order as $i) {
                $identifiers[] = $this->simpleAssociableChoice[$i]->attrs['identifier'];
                $result .= $this->simpleAssociableChoice[$i]->__invoke($controller);
            }
        }

        $result .= "</ol>";

        // Now create however many empty associations are required
        $maxAssociations = $this->attrs['maxAssociations'];

        // This is horrible but what else can we do without Javascript?
        if ($maxAssociations == 0) {
            $maxAssociations = count($this->simpleAssociableChoice) *
            count($this->simpleAssociableChoice);
        }

        for($i = 0; $i < $maxAssociations; $i++) {

            $inputs = "<div>";

            $inputs .= "<select name=\"{$variableName}[]\"><option></option>";
            $leftnumber = 1;
            foreach($identifiers as $left) {
                $rightnumber = 1;
                foreach($identifiers as $right) {
                    if (isset($responseVariable->value[$i]) && $responseVariable->value[$i] == "{$left} {$right}") {
                        $selected = " selected=\"selected\" ";
                    } else {
                        $selected = '';
                    }
                    $inputs .= "<option value=\"{$left} {$right}\" $selected>" . $leftnumber . ", " . $rightnumber++ . "</option>";
                }
                $leftnumber++;
            }
            $inputs .= "</select>";

            $result .= $inputs;
        }

        $result .= "</div>";
        return $result;
    }

}