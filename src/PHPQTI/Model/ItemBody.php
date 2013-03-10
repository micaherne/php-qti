<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\BodyElement;

class ItemBody extends \PHPQTI\Model\Gen\ItemBody implements BodyElement {

    protected $_elementName = 'itemBody';

    public function __invoke($controller) {
        $result = "<div";
        if(!empty($attrs)) { // add stuff like "class" attribute
            foreach($attrs as $key => $value) {
                $result .= " $key=\"$value\"";
            }
        }
        $result .= ">";
        foreach($this->_children as $child) {
            $result .= $child($controller);
        }
        $result .= "</div>";
        return $result;
    }
}