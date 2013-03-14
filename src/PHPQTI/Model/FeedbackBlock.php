<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\InlineStatic;
use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\FeedbackElement;
use PHPQTI\Model\Base\BodyElement;

class FeedbackBlock extends \PHPQTI\Model\Gen\FeedbackBlock implements FeedbackElement, BodyElement, FlowStatic, InlineStatic {

    protected $_elementName = 'feedbackBlock';

    public function __invoke($controller) {
        if ($controller->showFeedback($this)) {
            $result = "<div class=\"{$this->cssClass()}\">";
            foreach ($this->_children as $child) {
                $result .= $child->__invoke($controller);
            }
            $result .= '</div>';
            return $result;
        } else {
            return '';
        }
    }
}