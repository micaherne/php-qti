<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class QTIDefault extends \PHPQTI\Model\Gen\QTIDefault implements Expression {

    protected $_elementName = 'default';

    public function __invoke($controller) {
        $varname = $this->identifier;
        if(isset($controller->response[$varname])) {
            return $controller->response[$varname]->getDefaultValue();
        } else if (isset($controller->outcome[$varname])) {
            return $controller->outcome[$varname]->getDefaultValue();
        } else if (isset($controller->template[$varname])) {
            return $controller->tempate[$varname]->getDefaultValue();
        } else {
            throw new ProcessingException("QTIVariable $varname not found");
        }
    }
}