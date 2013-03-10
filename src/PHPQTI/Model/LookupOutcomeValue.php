<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\OutcomeRule;

use PHPQTI\Model\Base\ResponseRule;

class LookupOutcomeValue extends \PHPQTI\Model\Gen\LookupOutcomeValue 
    implements ResponseRule, OutcomeRule {

    protected $_elementName = 'lookupOutcomeValue';

    
}