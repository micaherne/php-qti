<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\ItemSubset;
use PHPQTI\Model\Base\Expression;

class NumberCorrect extends \PHPQTI\Model\Gen\NumberCorrect implements Expression,
    ItemSubset {

    protected $_elementName = 'numberCorrect';

    
}