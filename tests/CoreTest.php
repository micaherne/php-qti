<?php

class CoreTest extends PHPUnit_Framework_TestCase {
    
    public function testOne() {
        $this->assertTrue(true);
    }
    
    public function testTwo() {
        $g = new PHPQTI\Generate\FunctionGenerator();
        $f = $g->div();
        $this->assertEquals($f(null), '<div></div>');
        
        $f2 = $g->outcomeDeclaration(array('identifier' => 'CLOSED', 'cardinality' => 'multiple', 'baseType' => 'identifier'),
                $g->defaultValue(array(),
                        $g->value(array(),
                                $g->__text('DoorA')),
                        $g->value(array(),
                                $g->__text('DoorB')),
                        $g->value(array(),
                                $g->__text('DoorC'))));
        
        
        $f3 = $g->prompt($g->__text('DoorC'));

        echo $f3(null);
    }
    
}