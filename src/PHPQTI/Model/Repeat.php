<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Repeat extends \PHPQTI\Model\Gen\Repeat implements Expression {

    protected $_elementName = 'repeat';

    public function __invoke($controller) {
        $numberRepeats = $controller->valueOrVariable($this->numberRepeats);
        $vars = array();
        foreach($this->_children as $child) {
            $vars[] = $child($controller);
        }
        return QTIVariable::repeat($numberRepeats, $vars);
    }
}