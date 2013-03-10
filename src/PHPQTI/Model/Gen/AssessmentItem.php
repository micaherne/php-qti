<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class AssessmentItem extends AbstractClass {

    protected $_elementName = 'assessmentItem';

    public $identifier;
    public $title;
    public $label;
    public $toolName;
    public $toolVersion;
    public $adaptive;
    public $timeDependent;

}