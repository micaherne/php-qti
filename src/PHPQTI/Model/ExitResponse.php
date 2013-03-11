<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\ResponseRule;

class ExitResponse extends \PHPQTI\Model\Gen\ExitResponse implements ResponseRule {

    protected $_elementName = 'exitResponse';

    public function __invoke($controller) {
    	throw new ExitResponseException();
    }
}