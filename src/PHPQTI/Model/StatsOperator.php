<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;
use PHPQTI\Runtime\QTIVariable;

class StatsOperator extends \PHPQTI\Model\Gen\StatsOperator implements Expression {

    protected $_elementName = 'statsOperator';

    public function __invoke($controller) {
        return QTIVariable::statsOperator($this->name, $this->_children[0]($controller));
    }
}