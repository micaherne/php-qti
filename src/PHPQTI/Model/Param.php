<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\ObjectFlow;

class Param extends \PHPQTI\Model\Gen\Param implements ObjectFlow {

    protected $_elementName = 'param';

    public function __invoke($controller) {
        $result = "<param";
        if(!empty($attrs)) {
            foreach($attrs as $key => $value) {
                if ($key == 'value'
                        && isset($controller->template[$value]) 
                        && $controller->template[$value]->paramVariable) {
                            $result .= ' value="' . $controller->template[$value]->value . '"';
                } else {
                    $result .= " $key=\"$value\"";
                }
            }
        }
        $result .= ">";
        if(!empty($this->_children)) {
            foreach($this->_children as $child) {
                $result .= $child($controller);
            }
        }
        $result .= "</param>";
        return $result;
    }
}