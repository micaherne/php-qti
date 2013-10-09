<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;
use PHPQTI\Runtime\QTIVariable;

class Repeat extends \PHPQTI\Model\Gen\Repeat implements Expression {

    protected $_elementName = 'repeat';

    public function __invoke($controller) {
        $numberRepeats = $controller->valueOrVariable($this->numberRepeats);
        $vars = array();
        
        // We need to actually evaluate the expression multiple times, not just repeat the result
        for ($i = 0; $i < $numberRepeats; $i++) {
            foreach($this->_children as $child) {
                $vars[] = $child($controller);
            }
        }
        
        $value = array();
        foreach($vars as $var) {
            $value[] = $var->value;
        }
        
        $result = new QTIVariable('ordered', $vars[0]->type, array('value' => $value));

        return $result;
    }
}