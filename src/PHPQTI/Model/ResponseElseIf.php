<?php
 
namespace PHPQTI\Model;
 
class ResponseElseIf extends \PHPQTI\Model\Gen\ResponseElseIf {

    protected $_elementName = 'responseElseIf';

        // Identical to responseIf
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