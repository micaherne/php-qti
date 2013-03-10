<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\StringInteraction;
use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\Block;
use PHPQTI\Model\Base\BlockInteraction;

use PHPQTI\Model\Prompt;

class ExtendedTextInteraction extends \PHPQTI\Model\Gen\ExtendedTextInteraction 
    implements StringInteraction, BlockInteraction, Block, Flow, Interaction {

    protected $_elementName = 'extendedTextInteraction';

    public function __invoke($controller) {
        $variableName = $this->responseIdentifier;
        $variable = $controller->response[$variableName];
        $result = '';
    
        // Process child nodes
        foreach($this->_children as $child) {
            if ($child instanceof Prompt) {
                $result .= $child($controller);
            }
        }
    
        if ($variable->cardinality == 'single') {
            $brackets = '';
            $values = array($variable->value);
            $count = 1;
        } else {
            $brackets = '[]';
            $values = $variable->value;
            $count = $this->maxStrings;
        }

        for($i = 0; $i < $count; $i++) {
            if(isset($values[$i])) {
                $value = $values[$i];
            } else {
                $value = '';
            }
            $result .= "<textarea name=\"{$variableName}{$brackets}\">" . htmlentities($value) . "</textarea>";
        }
        return $result;
    }
    
}