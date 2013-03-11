<?php
 
namespace PHPQTI\Model;
 
class ModalFeedback extends \PHPQTI\Model\Gen\ModalFeedback {

    protected $_elementName = 'modalFeedback';

    public function __invoke($controller) {
        if ($controller->showFeedback($this)) {
            $result = "<span class=\"{$this->cssClass()}\">";
            foreach ($this->_children as $child) {
                $result .= $child->__invoke($controller);
            }
            $result .= '</span>';
            return $result;
        } else {
            return '';
        }
    }
}