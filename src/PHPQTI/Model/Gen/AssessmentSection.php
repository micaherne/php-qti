<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class AssessmentSection extends AbstractClass {

    protected $_elementName = 'assessmentSection';

    public $identifier;
    public $required;
    public $fixed;
    public $title;
    public $visible;
    public $keepTogether;

}