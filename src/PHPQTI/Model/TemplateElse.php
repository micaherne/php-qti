<?php
 
namespace PHPQTI\Model;
 
class TemplateElse extends \PHPQTI\Model\Gen\TemplateElse {

    protected $_elementName = 'templateElse';

    public function __invoke($controller) {
        for($i = 0; $i < count($this->_children); $i++) {
            $this->_children[$i]($controller);
        }
    }
}