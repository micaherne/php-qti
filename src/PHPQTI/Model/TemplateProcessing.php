<?php
 
namespace PHPQTI\Model;
 
class TemplateProcessing extends \PHPQTI\Model\Gen\TemplateProcessing {

    protected $_elementName = 'templateProcessing';

    public function __invoke($controller) {
    		try {
	    		foreach($this->_children as $child) {
	                $child($controller);
	            }
        } catch (ExitTemplateException $e) {
        	// stop processing immediately
        	return;
        } catch (TemplateConditionException $e) {
            // restart template processing
            $controller->doTemplateCondition();
        }
    }
}