<?php
 
namespace PHPQTI\Model;
 
class TemplateIf extends \PHPQTI\Model\Gen\TemplateIf {

    protected $_elementName = 'templateIf';

    public function __invoke($controller) {
        $result = $this->_children[0]($controller);
        if ($result->value === true) {
            for($i = 1; $i < count($this->_children); $i++) {
                $this->_children[$i]($controller);
            }
        }
        return $result;
    }
}