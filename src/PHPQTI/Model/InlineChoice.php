<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Choice;

class InlineChoice extends \PHPQTI\Model\Gen\InlineChoice implements Choice {

    protected $_elementName = 'inlineChoice';

    public function __invoke($controller) {
        $identifier = $this->identifier;
    
        // See if this response was selected already
    
        if ($controller->response[$this->name]->value == $identifier) {
            $selected = ' selected="selected" ';
        } else {
            $selected = '';
        }
        $result = '<option value="' . $identifier . "\" $selected>";
        foreach($this->_children as $child) { // should be only one
            $result .= $child->__invoke($controller);
        }
        $result .= '</option>';
        return $result;
    }
}