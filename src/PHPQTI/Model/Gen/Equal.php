<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class Equal extends AbstractClass {

    protected $_elementName = 'equal';

    public $toleranceMode;
    public $tolerance;
    public $includeLowerBound;
    public $includeUpperBound;

}