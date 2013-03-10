<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class NumberSelected extends AbstractClass {

    protected $_elementName = 'numberSelected';

    public $sectionIdentifier;
    public $includeCategory;
    public $excludeCategory;

}