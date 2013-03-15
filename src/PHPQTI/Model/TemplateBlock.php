<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BlockStatic;
use PHPQTI\Model\Base\TemplateElement;

class TemplateBlock extends \PHPQTI\Model\Gen\TemplateBlock implements TemplateElement,
    BlockStatic, FlowStatic {

    protected $_elementName = 'templateBlock';

    public function __invoke($controller) {
        if ($controller->showTemplate($this)) {
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