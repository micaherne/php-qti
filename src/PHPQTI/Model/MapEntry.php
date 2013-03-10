<?php
 
namespace PHPQTI\Model;
 
class MapEntry extends \PHPQTI\Model\Gen\MapEntry {

    protected $_elementName = 'mapEntry';

    public function __invoke($controller) {
        return $this; // mapEntry has no inherent functionality;
    }
    
}