<?php

namespace PHPQTI\Model\MathML;

use PHPQTI\Model\Base\AbstractClass;

class MathMLController extends AbstractClass {
    
    protected $xml;
    
    public function __construct($xml) {
        $this->xml = $xml;
    }
    
    public function __invoke($controller) {
        return $this->xml;
    }
}