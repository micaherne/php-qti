<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class OutcomeMinimum extends AbstractClass {

    protected $_elementName = 'outcomeMinimum';

    public $sectionIdentifier;
    public $includeCategory;
    public $excludeCategory;
    public $outcomeIdentifier;
    public $weightIdentifier;

}