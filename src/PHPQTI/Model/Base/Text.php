<?php

namespace PHPQTI\Model\Base;

class Text extends AbstractClass {
    
    protected $text = '';
    
    public function __construct($text) {
        $this->text = $text;
    }
    
    public function __invoke($controller) {
        return $this->text;
    }

}