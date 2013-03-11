<?php
 
namespace PHPQTI\Model;
 
class ResponseProcessing extends \PHPQTI\Model\Gen\ResponseProcessing {

    protected $_elementName = 'responseProcessing';

    public function __invoke($controller) {
    	try {
            foreach($this->_children as $child) {
                $child($controller);
            }
    	} catch (ExitResponseException $e) {
    		// stop processing immediately
    		return;
    	}
    }
}