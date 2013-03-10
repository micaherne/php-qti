<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\ItemSubset;
use PHPQTI\Model\Base\Expression;

class NumberResponded extends \PHPQTI\Model\Gen\NumberResponded implements Expression,
    ItemSubset {

    protected $_elementName = 'numberResponded';

    
}