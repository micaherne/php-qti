<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class ContainerSize extends \PHPQTI\Model\Gen\ContainerSize implements Expression {

    protected $_elementName = 'containerSize';

    public function __invoke($controller) {
        $container = $child($controller);
        return $container->containerSize();
    }
}