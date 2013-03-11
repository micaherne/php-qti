<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Runtime\QTIVariable;

use PHPQTI\Model\Base\Expression;

class Lcm extends \PHPQTI\Model\Gen\Lcm implements Expression {

    protected $_elementName = 'lcm';

    public function __invoke($controller) {
    	$vars = array();
    	foreach($this->_children as $child) {
    		$vars[] = $child($controller);
    	}
    	return QTIVariable::lcm($vars);
    }
}