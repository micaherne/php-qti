<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class AssessmentItemRef extends AbstractClass {

    protected $_elementName = 'assessmentItemRef';

    public $identifier;
    public $required;
    public $fixed;
    public $href;
    public $category;

}