<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\TemplateRule;

class ExitTemplate extends \PHPQTI\Model\Gen\ExitTemplate implements TemplateRule {

    protected $_elementName = 'exitTemplate';

    public function __invoke($controller) {
    	throw new ExitTemplateException();
    	}
}