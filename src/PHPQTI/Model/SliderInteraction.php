<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\Block;
use PHPQTI\Model\Base\BlockInteraction;

// TODO: Show min and max labels at either end. Support stepLabel and reverse
class SliderInteraction extends \PHPQTI\Model\Gen\SliderInteraction 
    implements BlockInteraction, Block, Flow, Interaction {

    protected $_elementName = 'sliderInteraction';

    public function __invoke($controller) {
        $variableName = $this->responseIdentifier;
        $value = $controller->response[$variableName]->getValue();
    
        $result = '';
    
        foreach($this->_children as $child) {
            if ($child instanceof Prompt) {
                $result .= $child->__invoke($controller);
            }
        }
    
        $result .= "<div class=\"qti_sliderInteraction\" ";
        $result .= implode(' ', $this->_getDataAttributes());
        $result .= "> <div class=\"value\"></div>";
        $result .= "<div class=\"slider\" /></div> ";
        $result .= "<input type=\"hidden\" name=\"{$variableName}\" value=\"{$value}\" />";
        $result .= "</div>";
        return $result;
    }
    
}