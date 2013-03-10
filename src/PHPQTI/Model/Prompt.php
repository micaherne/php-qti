<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\BodyElement;

class Prompt extends \PHPQTI\Model\Gen\Prompt implements BodyElement {

    protected $_elementName = 'prompt';

    public function __invoke($controller) {
        $result = '<div class="qti_prompt">';
        foreach($this->_children as $child) {
            $result .= $child($controller);
        }
        $result .= '</div>';
        return $result;
        
    }
    
}