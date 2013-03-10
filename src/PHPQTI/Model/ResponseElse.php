<?php
 
namespace PHPQTI\Model;
 
class ResponseElse extends \PHPQTI\Model\Gen\ResponseElse {

    protected $_elementName = 'responseElse';

    public function __invoke($controller) {
        for($i = 0; $i < count($this->_children); $i++) {
            $this->_children[$i]($controller);
        }
    }
}