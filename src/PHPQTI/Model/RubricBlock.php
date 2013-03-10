<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\InlineStatic;
use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;

use PHPQTI\Model\Enumeration\View;

class RubricBlock extends \PHPQTI\Model\Gen\RubricBlock implements BodyElement, FlowStatic, InlineStatic {

    protected $_elementName = 'rubricBlock';
    
    public function __invoke($controller) {
        echo "Controller {$controller->view}, this {$this->view}";
        if (true || $controller->view == $this->view) {
            $result = '<div class="qti_rubricBlock">';
            foreach($this->_children as $child) {
                $result .= $child($controller);
            }
            $result .= '</div>';
            return $result;
        }
    }

    
}