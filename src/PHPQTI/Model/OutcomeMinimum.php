<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\ItemSubset;
use PHPQTI\Model\Base\Expression;

class OutcomeMinimum extends \PHPQTI\Model\Gen\OutcomeMinimum implements Expression,
    ItemSubset {

    protected $_elementName = 'outcomeMinimum';

    
}