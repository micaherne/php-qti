<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\ResponseRule;

class ResponseCondition extends \PHPQTI\Model\Gen\ResponseCondition 
    implements ResponseRule {

    protected $_elementName = 'responseCondition';

    public function __invoke($controller) {
        foreach($this->_children as $child) {
            $result = $child($controller);
            if (isset($result->value) && $result->value === true) {
                return;
            }
        }
    }
}