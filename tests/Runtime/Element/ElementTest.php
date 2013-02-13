<?php

use PHPQTI\Runtime\Element\EndAttemptInteraction;

class ElementTest extends PHPUnit_Framework_TestCase {
    
    public function testCssClass() {
        $element = new EndAttemptInteraction(null, null);
        $this->assertEquals('qti_endAttemptInteraction', $element->cssClass());
    }
    
}