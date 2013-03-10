<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\ItemSubset;

use PHPQTI\Model\Base\Expression;

class NumberIncorrect extends \PHPQTI\Model\Gen\NumberIncorrect implements Expression,
    ItemSubset {

    protected $_elementName = 'numberIncorrect';

    
}