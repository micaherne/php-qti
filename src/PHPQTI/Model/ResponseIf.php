<?php
 
namespace PHPQTI\Model;
 
class ResponseIf extends \PHPQTI\Model\Gen\ResponseIf {

    protected $_elementName = 'responseIf';

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