<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class MapResponse extends \PHPQTI\Model\Gen\MapResponse implements Expression {

    protected $_elementName = 'mapResponse';

    public function __invoke($controller) {
        $varname = $this->identifier;
        if(isset($controller->response[$varname])) {
            return $controller->response[$varname]->mapResponse();
        } else {
            throw new ProcessingException("QTIVariable $varname not found");
        }
    }
}