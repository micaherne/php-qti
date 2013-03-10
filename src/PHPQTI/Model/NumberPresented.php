<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\ItemSubset;
use PHPQTI\Model\Base\Expression;

class NumberPresented extends \PHPQTI\Model\Gen\NumberPresented implements Expression,
    ItemSubset {

    protected $_elementName = 'numberPresented';

    
}