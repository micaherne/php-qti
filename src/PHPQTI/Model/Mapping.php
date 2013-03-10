<?php
 
namespace PHPQTI\Model;
 
class Mapping extends \PHPQTI\Model\Gen\Mapping {

    public function __invoke($controller) {
        return $this; // mapping has no inherent functionality
    }

}