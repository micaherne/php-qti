<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class TimeLimits extends AbstractClass {

    protected $_elementName = 'timeLimits';

    public $minTime;
    public $maxTime;
    public $allowLateSubmission;

}