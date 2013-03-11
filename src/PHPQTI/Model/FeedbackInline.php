<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\FeedbackElement;

use PHPQTI\Model\Base\InlineStatic;
use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;
use PHPQTI\Model\Base\SimpleInline;
 
class FeedbackInline extends \PHPQTI\Model\Gen\FeedbackInline implements FeedbackElement, SimpleInline, BodyElement, FlowStatic, InlineStatic {

    protected $_elementName = 'feedbackInline';
    
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