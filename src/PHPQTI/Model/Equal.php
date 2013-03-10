<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Equal extends \PHPQTI\Model\Gen\Equal implements Expression {

    protected $_elementName = 'equal';

    public function __invoke($controller) {
        $toleranceMode = $this->toleranceMode;
        $tolerance = array();
        if (isset($this->tolerance)) {
            $toleranceAttrs = preg_split('/\w+/', $this->tolerance);
            foreach($toleranceAttrs as $toleranceAttr) {
                $tolerance[] = $controller->valueOrVariable($toleranceAttr);
            }
        }
        $includeLowerBound = true;
        if (isset($this->includeLowerBound)) {
            $includeLowerBound = ($this->includeLowerBound != 'false');
        }
        $includeUpperBound = true;
        if (isset($this->includeUpperBound)) {
            $includeUpperBound = ($this->includeUpperBound != 'false');
        }
        
        $val1 = $this->_children[0]($controller);
        $val2 = $this->_children[1]($controller);
        
        return $val1->equal($val2, $toleranceMode, $tolerance, $includeLowerBound, $includeUpperBound);
    }
}