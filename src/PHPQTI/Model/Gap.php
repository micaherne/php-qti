<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\AssociableChoice;

class Gap extends \PHPQTI\Model\Gen\Gap implements AssociableChoice {

    protected $_elementName = 'gap';

    public function __invoke($controller) {
        $gapMatchInteraction = $this->findAncestorWithInterface('PHPQTI\Model\Base\Interaction');
        $identifier = $this->identifier;
        if (isset($this->required) && $this->required == 'true') {
            $required = ' required = "required" ';
        } else {
            $required = '';
        }
        $result = "<span class=\"qti_gap\"><select name=\"{$gapMatchInteraction->responseIdentifier}[{$identifier}]\" $required>";
        $result .= "<option></option>";
        foreach($gapMatchInteraction->gapChoice as $choice) {
            $variable = $controller->response[$gapMatchInteraction->responseIdentifier];
            $directedPairString = $choice->identifier . ' ' . $identifier;
    
            // Select correct options if we already have a value (i.e. after end attempt)
            if (!empty($variable->value)) {
                if ($variable->cardinality == 'single') {
                    $selected = ($variable->value ==  $directedPairString ? ' selected="selected"' : '');
                } else if ($variable->cardinality == 'multiple') {
                    $selected = (in_array($directedPairString, $variable->value) ? ' selected="selected"' : '');
                }
            } else {
                $selected = '';
            }
    
            $result .= "<option value=\"{$choice->identifier}\" $selected>";
            foreach($choice->_children as $child) {
                $result .= $child->__invoke($controller);
            }
            $result .= "</option>";
        }
        $result .= '</select></span>';
        return $result;
    }
}