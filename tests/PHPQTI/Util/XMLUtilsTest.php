<?php

use PHPQTI\Util\XMLUtils;

class XMLUtilsTest extends PHPUnit_Framework_TestCase {
    
    function testClassName() {
        $this->assertEquals('ChoiceInteraction', XMLUtils::className('choiceInteraction'));
        $this->assertEquals('QTIAnd', XMLUtils::className('and'));
    }
    
    function testUnmarshall() {
        $dom = new \DOMDocument();
        
        // Test actual object
        $this->assertTrue($dom->loadXML('<b xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" id="test" class="nim" label="bim">Hello there!</b>'));
        $b = $xmlutils->unmarshall($dom);
        $this->assertEquals('test', $b->id);
        $this->assertEquals('nim', $b->class);
        $this->assertEquals('bim', $b->label);
        
    }
    
}
