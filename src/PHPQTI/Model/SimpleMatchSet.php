<?php
 
namespace PHPQTI\Model;

use PHPQTI\Util\ChoiceIterator;

class SimpleMatchSet extends \PHPQTI\Model\Gen\SimpleMatchSet {

    protected $_elementName = 'simpleMatchSet';

    public function iterator($shuffle = false) {
    	return new ChoiceIterator($this->_children, $shuffle);
    }
}