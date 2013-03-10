<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\ItemSubset;
use PHPQTI\Model\Base\Expression;

class TestVariables extends \PHPQTI\Model\Gen\TestVariables implements Expression,
    ItemSubset {

    protected $_elementName = 'testVariables';

    
}