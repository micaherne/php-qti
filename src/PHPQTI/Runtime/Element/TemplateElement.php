<?php

// TODO: Refactor this and feedbackElement to common base class
abstract class TemplateElement extends Element {

    public function __invoke($controller) {
        $templateIdentifier = $this->attrs['templateIdentifier'];
        $showHide = $this->attrs['showHide'];
        $identifier = $this->attrs['identifier'];

        $class = get_class($this); // for CSS class

        if (!$variable = $controller->template[$templateIdentifier]) {
            return '';
        }

        // Create new variable for comparison
        /*
        * TODO: It looks from the examples as if it should be possible to have
        * a single "identifier" attribute representing multiple items (space delimited), but
        * the spec doesn't seem to mention this that I can find.
        *
        */
        $testvar = new Variable('single', $variable->type, array('value' => $identifier));
        if ($variable->cardinality == 'multiple') {
            $comparisonresult = $variable->contains($testvar);
        } else {
            $comparisonresult = $variable->match($testvar);
        }

        if ($comparisonresult->value && $showHide == 'show') {
            $result = "<span class=\"{$this->cssClass()}\">";
            foreach ($this->children as $child) {
                $result .= $child->__invoke($controller);
            }
            $result .= '</span>';
            return $result;
        } else if (!$comparisonresult->value && $showHide == 'hide') {
            $result = "<span class=\"{$this->cssClass()}\">";
            foreach ($this->children as $child) {
                $result .= $child->__invoke($controller);
            }
            $result .= '</span>';
            return $result;
        }
        return '';
    }

}