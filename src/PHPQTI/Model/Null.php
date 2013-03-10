<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class Null extends \PHPQTI\Model\Gen\Null implements Expression {

    protected $_elementName = 'null';

        // Create as single identifier, although it can be matched against any other null
    public function __invoke($controller) {
        return new QTIVariable('single', 'identifier', array(
                'value' => null
        ));
    }
}