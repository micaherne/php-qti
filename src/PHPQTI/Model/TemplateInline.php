<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\FlowStatic;

use PHPQTI\Model\Base\BlockStatic;

use PHPQTI\Model\Base\TemplateElement;

class TemplateInline extends \PHPQTI\Model\Gen\TemplateInline implements 
    BlockStatic, FlowStatic, TemplateElement {

    protected $_elementName = 'templateInline';

    public function __invoke($controller) {
        if ($controller->showTemplate($this)) {
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