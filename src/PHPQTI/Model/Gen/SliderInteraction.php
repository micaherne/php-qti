<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class SliderInteraction extends AbstractClass {

    protected $_elementName = 'sliderInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $lowerBound;
    public $upperBound;
    public $step;
    public $stepLabel;
    public $orientation;
    public $reverse;

}