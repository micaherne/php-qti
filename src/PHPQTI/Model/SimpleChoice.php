<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Choice;

class SimpleChoice extends \PHPQTI\Model\Gen\SimpleChoice implements Choice {

    protected $_elementName = 'simpleChoice';

    public function __invoke($controller) {
        // TODO: Do we need to check for non-existences here? Only hides if specifically hidden
        if (isset($this->templateIdentifier) && isset($this->showHide)) {
            $templateIdentifier = $this->templateIdentifier;
            $showHide = $this->showHide;;
            if(isset($controller->template[$templateIdentifier])) {
                $value = $controller->template[$templateIdentifier]->value;
                if((is_array($value) && in_array($this->identifier, $value)) || ($value == $this->identifier)) {
                    if($showHide == 'hide') {
                        return '';
                    }
                }
            }
        }
        $result = "<span class=\"qti_simpleChoice\">\n";
        
        $parentInteraction = $this->findAncestorWithInterface('\PHPQTI\Model\Base\Interaction');
        
        if (is_null($parentInteraction)) {
            throw new \Exception("Can't find parent interaction");
        } else {
            $interactionType = get_class($parentInteraction);
        }
        if ($interactionType == 'PHPQTI\Model\ChoiceInteraction') {
        
            // str_replace is for checkboxes where the element name always has [] at the end
            $responseValue = $controller->response[str_replace('[]', '', $this->name)]->value;
        
            // See if this response was selected already
            // TODO: Do this checking in qti_variable so it can be reused
            if (is_array($responseValue)) {
                $checked = in_array($this->identifier, $responseValue) ? ' checked="checked"' : '';
            } else {
                $checked = $responseValue == $this->identifier ? ' checked="checked"' : '';
            }
            $result .= "<input type=\"{$this->inputType}\" name=\"{$this->name}\" value=\"{$this->identifier}\" $checked></input>\n";
        } else if ($interactionType = 'PHPQTI\Model\OrderInteraction') {
            $result .= "<select name=\"{$this->name}[{$this->identifier}]\">\n";
            $result .= "<option></option>";
            for($i = 1; $i <= $this->numberOfChoices; $i++) {
                $selected = $controller->response[$this->name]->value[$i - 1] == $this->identifier ? ' selected="selected"' : '';
                $result .= "<option value=\"$i\" $selected>$i</option>";
            }
            $result .= "</select>";
        }
        foreach($this->_children as $child) {
            $result .= $child($controller);
        }
        $result .= "</span>";
        return $result;
    }
}