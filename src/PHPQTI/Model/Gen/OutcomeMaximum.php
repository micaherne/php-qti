<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class OutcomeMaximum extends AbstractClass {

    protected $_elementName = 'outcomeMaximum';

    public $sectionIdentifier;
    public $includeCategory;
    public $excludeCategory;
    public $outcomeIdentifier;
    public $weightIdentifier;

}