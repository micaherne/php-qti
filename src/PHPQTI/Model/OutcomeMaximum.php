<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\ItemSubset;
use PHPQTI\Model\Base\Expression;

class OutcomeMaximum extends \PHPQTI\Model\Gen\OutcomeMaximum implements Expression,
    ItemSubset {

    protected $_elementName = 'outcomeMaximum';

    
}