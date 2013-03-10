<?php
 
namespace PHPQTI\Model\Gen;

use PHPQTI\Model\Base\AbstractClass;
 
class ItemSessionControl extends AbstractClass {

    protected $_elementName = 'itemSessionControl';

    public $maxAttempts;
    public $showFeedback;
    public $allowReview;
    public $showSolution;
    public $allowComment;
    public $allowSkipping;
    public $validateResponses;

}