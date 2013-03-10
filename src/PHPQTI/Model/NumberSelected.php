<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\ItemSubset;
use PHPQTI\Model\Base\Expression;

class NumberSelected extends \PHPQTI\Model\Gen\NumberSelected implements Expression,
    ItemSubset {

    protected $_elementName = 'numberSelected';

    
}