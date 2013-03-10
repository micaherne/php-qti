<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\InlineStatic;
use PHPQTI\Model\Base\FlowStatic;
use PHPQTI\Model\Base\BodyElement;
use PHPQTI\Model\Base\AtomicInline;
 
class Img extends \PHPQTI\Model\Gen\Img implements AtomicInline, BodyElement, FlowStatic, InlineStatic {

    protected $_elementName = 'img';

    public function __invoke($controller) {
        if(isset($this->src)) {
            $this->src = $controller->resource_provider->urlFor($this->src);
        }
        return parent::__invoke($controller);
    }
}