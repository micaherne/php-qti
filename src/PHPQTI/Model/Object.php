<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\InlineStatic;
use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;

class Object extends \PHPQTI\Model\Gen\Object implements BodyElement, FlowStatic, InlineStatic {

    protected $_elementName = 'object';

    public function __invoke($controller) {
        if(isset($this->data)) {
            $this->data = $controller->resource_provider->urlFor($this->data);
        }
        return parent::__invoke($controller);
    }
}