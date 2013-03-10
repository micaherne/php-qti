<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class RoundTo extends AbstractClass {

    protected $_elementName = 'roundTo';

    public $roundingMode;
    public $figures;

}