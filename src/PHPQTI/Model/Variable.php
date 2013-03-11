<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;
use PHPQTI\Runtime\Exception\ProcessingException;

class Variable extends \PHPQTI\Model\Gen\Variable implements Expression {

    protected $_elementName = 'variable';

    public function __invoke($controller) {
        $varname = $this->identifier;
        if(isset($controller->response[$varname])) {
            return $controller->response[$varname];
        } else if (isset($controller->outcome[$varname])) {
            return $controller->outcome[$varname];
        } else if (isset($controller->template[$varname])) {
            return $controller->template[$varname];
        } else {
            throw new ProcessingException("Variable $varname not found");
        }
    }
    
}