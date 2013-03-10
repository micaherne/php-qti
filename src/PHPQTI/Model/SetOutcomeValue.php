<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\OutcomeRule;

use PHPQTI\Model\Base\ResponseRule;

class SetOutcomeValue extends \PHPQTI\Model\Gen\SetOutcomeValue 
    implements ResponseRule, OutcomeRule {

    protected $_elementName = 'setOutcomeValue';

    public function __invoke($controller) {
        $varname = $this->identifier;
        $controller->outcome[$varname] = $this->_children[0]($controller);
    }
}