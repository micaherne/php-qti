<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class NumberIncorrect extends AbstractClass {

    protected $_elementName = 'numberIncorrect';

    public $sectionIdentifier;
    public $includeCategory;
    public $excludeCategory;

}