<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class MapResponsePoint extends \PHPQTI\Model\Gen\MapResponsePoint implements Expression {

    protected $_elementName = 'mapResponsePoint';

    public function __invoke($controller) {
        $varname = $this->identifier;
        if(isset($controller->response[$varname])) {
            return $controller->response[$varname]->mapResponsePoint();
        } else {
            throw new ProcessingException("QTIVariable $varname not found");
        }
    }
}