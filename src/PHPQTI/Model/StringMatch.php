<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Expression;

class StringMatch extends \PHPQTI\Model\Gen\StringMatch implements Expression {

    protected $_elementName = 'stringMatch';

    public function __invoke($controller) {
        $val1 = $this->_children[0]($controller);
        $val2 = $this->_children[1]($controller);
        
        if (isset($this->substring)) {
            $substring = $this->substring;
        } else {
            $substring = 'false';
        }
        // TODO: Missing substring attribute will probably break helper function
        return $val1->stringMatch($val2, $this->caseSensitive, $substring);
    }
}