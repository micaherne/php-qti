<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class RoundTo extends \PHPQTI\Model\Gen\RoundTo implements Expression {

    protected $_elementName = 'roundTo';

    public function __invoke($controller) {
        $figures = $controller->valueOrVariable($this->figures);
        if (isset($this->roundingMode)) {
            $roundingMode = $this->roundingMode;
        } else {
            $roundingMode = 'significantFigures';
        }
        $val1 = $this->_children[0]($controller);
        return $val1->roundTo($figures, $roundingMode);
    }
}