<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class EqualRounded extends \PHPQTI\Model\Gen\EqualRounded implements Expression {

    protected $_elementName = 'equalRounded';

    public function __invoke($controller) {
        $val1 = $this->_children[0]($controller);
        $val2 = $this->_children[1]($controller);
        $figures = $controller->valueOrVariable($this->figures);
        if (isset($this->roundingMode)) {
            $roundingMode = $this->roundingMode;
        } else {
            $roundingMode = 'significantFigures';
        }
        return $val1->equalRounded($val2, $figures, $roundingMode);
    }
}