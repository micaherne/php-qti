<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Correct extends \PHPQTI\Model\Gen\Correct implements Expression {

    protected $_elementName = 'correct';

    public function __invoke($controller) {
        $varname = $this->identifier;
        if(isset($controller->response[$varname])) {
            return $controller->response[$varname]->getCorrectResponse();
        } else {
            throw new ProcessingException("QTIVariable $varname not found");
        }
    }
}