<?php

use PHPQTI\Model\B;

use PHPQTI\Model\ItemBody;

class TempTest extends PHPUnit_Framework_TestCase {
    
    public function testOne() {
        $obj = new B();
        $r = new ReflectionClass($obj);
        $this->assertTrue($r->implementsInterface('\PHPQTI\Model\Base\BodyElement'));
        $this->assertEquals('b', $obj->getElementName());
    }
    
    public function testTwo() {
        $dom = new \DOMDocument();
        $this->assertTrue($dom->loadXML('<b xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" id="test" class="nim" label="bim">Hello there!</b>'));
        
        $x = new \PHPQTI\Util\XMLUtils();
        $r = $x->unmarshall($dom);
    }
    
}