<?php

use PHPQTI\Model\MapEntry;

use PHPQTI\Model\Mapping;

use PHPQTI\Runtime\QTIVariable;

class VariableTest extends PHPUnit_Framework_TestCase {
    
    public function testToString() {
        
        $variable1 = new QTIVariable('single', 'integer', array('value' => 3));
        $this->assertEquals('single integer [3]', "" . $variable1);
        
        $variable2 = new QTIVariable('multiple', 'identifier', array('value' => array('A', 'B')));
        $this->assertEquals('multiple identifier [A,B]', $variable2);

    }
    
    public function testGetCorrectResponse() {
        $variable1 = new QTIVariable('single', 'integer', array('correctResponse' => 'This is correct'));;
        $this->assertEquals('This is correct', $variable1->correctResponse);
        $correct1 = $variable1->getCorrectResponse();
        $this->assertEquals('This is correct', $correct1->value);
    }
    
    public function testMultiple() {
        $variable1 = new QTIVariable('single', 'identifier', array('value' => 'thing1'));
        $variable2 = new QTIVariable('single', 'identifier', array('value' => 'thing2'));
        $variable3 = new QTIVariable('single', 'identifier', array('value' => 'thing3'));
        
        $result1 = QTIVariable::multiple($variable1, $variable2, $variable3);
        $this->assertEquals('multiple', $result1->cardinality);
        $this->assertEquals('identifier', $result1->type);
        $this->assertEquals(3, count($result1->value));
        
        $result2 = QTIVariable::multiple();
        $this->assertNull($result2->value);
        
        $null1 = new QTIVariable('single', 'identifier');
        $null2 = new QTIVariable('single', 'identifier');
        $result3 = QTIVariable::multiple($null1, $null2);
        $this->assertNull($result3->value);
        
        $variable4 = new QTIVariable('single', 'identifier', array('value' => 'tryAgain'));
        $result4 = QTIVariable::multiple($variable4);
        $this->assertEquals(1, count($result4->value));
        
        /* This is to check for an issue that occurred where multiple was being
* sent an array of variables as a single parameter, rather than as a list
* of parameters, and should have worked anyway.
*/
        $variable5 = array($variable4, $variable4);
        $result5 = QTIVariable::multiple($variable5);
        $this->assertEquals(2, count($result5->value));
    }
    
    public function testOrdered() {
        $variable1 = new QTIVariable('single', 'identifier', array('value' => 'thing1'));
        $variable2 = new QTIVariable('single', 'identifier', array('value' => 'thing2'));
        $variable3 = new QTIVariable('single', 'identifier', array('value' => 'thing3'));
    
        $result1 = QTIVariable::ordered($variable1, $variable2, $variable3);
        $this->assertEquals('ordered', $result1->cardinality);
        $this->assertEquals('identifier', $result1->type);
        $this->assertEquals(3, count($result1->value));
    }
    
    public function testContainerSize() {
        $variable1 = new QTIVariable('single', 'identifier', array('value' => 12));
        $result1 = $variable1->containerSize();
        $this->assertEquals(1, $result1->value);
        
        $variable2 = new QTIVariable('multiple', 'identifier', array('value' => array('thing1', 'thing2')));
        $result2 = $variable2->containerSize();
        $this->assertEquals(2, $result2->value);
    }
    
    public function testIsNull() {
        $variable1 = new QTIVariable('single', 'identifier');
        $result1 = $variable1->isNull();
        $this->assertEquals('single', $result1->cardinality);
        $this->assertEquals('boolean', $result1->type);
        $this->assertTrue($result1->value);
        
        // only empty strings and containers should be treated as null, not (e.g.) booleans
        $variable2 = new QTIVariable('single', 'boolean', array('value' => false));
        $this->assertFalse($variable2->isNull()->value);
    }
    
    public function testIndex() {
        $variable1 = new QTIVariable('multiple', 'identifier', array('value' => array('thing1', 'thing2')));
        $this->assertEquals("thing2", $variable1->index(2)->value);
        
        $variable2 = new QTIVariable('multiple', 'float');
        $this->assertNull($variable2->index(2)->value);
    }
    
    public function testRandom() {
        $variable1 = new QTIVariable('multiple', 'identifier', array('value' => array(2, 4, 6, 8, 10)));
        $result1 = $variable1->random();
        $this->assertEquals('single', $result1->cardinality);
        $this->assertEquals('identifier', $result1->type);
        $this->assertTrue($result1->value <= 10 && $result1->value % 2 == 0);
    }
    
    public function testMember() {
        $variable1 = new QTIVariable('single', 'identifier', array('value' => 6));
        $variable2 = new QTIVariable('multiple', 'identifier', array('value' => array(2, 4, 6, 8, 10)));
        $variable3 = new QTIVariable('single', 'identifier', array('value' => 5));
        
        $result1 = $variable1->member($variable2);
        $this->assertEquals('single', $result1->cardinality);
        $this->assertEquals('boolean', $result1->type);
        $this->assertTrue($result1->value);
        
        $result2 = $variable3->member($variable2);
        $this->assertFalse($result2->value);
    }
    
    public function testDelete() {
        $variable1 = new QTIVariable('single', 'identifier', array('value' => 6));
        $variable2 = new QTIVariable('multiple', 'identifier', array('value' => array(2, 4, 6, 8, 10)));
        
        $result1 = $variable1->delete($variable2);
        $this->assertEquals('multiple', $result1->cardinality);
        $this->assertEquals('identifier', $result1->type);
        $this->assertEquals(4, count($result1->value));
    
    }
    
    public function testContains() {
        $variable1 = new QTIVariable('single', 'identifier', array('value' => 6));
        $variable2 = new QTIVariable('multiple', 'identifier', array('value' => array(2, 4, 6, 8, 10)));
        $this->assertTrue($variable2->contains($variable1)->value);
        
        $variable3 = new QTIVariable('multiple', 'identifier', array('value' => array(6, 8)));
        $this->assertTrue($variable2->contains($variable3)->value);
        
        $variable4 = new QTIVariable('multiple', 'identifier', array('value' => array(6, 8, 8)));
        $this->assertFalse($variable2->contains($variable4)->value);
        
        // Test ordered
        $variable2->cardinality = 'ordered';
        $variable5 = new QTIVariable('ordered', 'identifier', array('value' => array(6, 8)));
        $this->assertTrue($variable2->contains($variable5)->value);
        
        $variable6 = new QTIVariable('ordered', 'identifier', array('value' => array(8, 6)));
        $this->assertFalse($variable2->contains($variable6)->value);
        
        $variable7 = new QTIVariable('ordered', 'identifier', array('value' => array(8, 10)));
        $this->assertTrue($variable2->contains($variable7)->value);
        
    }
    
    public function testSubstring() {
        $variable1 = new QTIVariable('single', 'string', array('value' => 'Scunthorpe'));
        $variable2 = new QTIVariable('single', 'string', array('value' => 'thor'));
        $this->assertTrue($variable2->substring($variable1)->value);
        
        $variable3 = new QTIVariable('single', 'string', array('value' => 'Thor'));
        $this->assertFalse($variable3->substring($variable1)->value);
        $this->assertTrue($variable3->substring($variable1, false)->value);
    }
    
    public function testNot() {
        $variable1 = new QTIVariable('single', 'boolean', array('value' => true));
        $this->assertFalse($variable1->not()->value);
        
        $variable1->value = false;
        $this->assertTrue($variable1->not()->value);
        
        $variable1->value = null;
        $this->assertTrue($variable1->not()->isNull()->value);
    }
    
    public function testAnd() {
        $variable1 = new QTIVariable('single', 'boolean', array('value' => true));
        $variable2 = new QTIVariable('single', 'boolean', array('value' => false));
        $this->assertTrue(QTIVariable::and_($variable1, $variable1)->value);
        $this->assertFalse(QTIVariable::and_($variable1, $variable2, $variable1)->value);
    }
    
    public function testOr() {
        $variable1 = new QTIVariable('single', 'boolean', array('value' => true));
        $variable2 = new QTIVariable('single', 'boolean', array('value' => false));
        $this->assertTrue(QTIVariable::or_($variable1, $variable1)->value);
        $this->assertTrue(QTIVariable::or_($variable2, $variable1, $variable2)->value);
        $this->assertFalse(QTIVariable::or_($variable2, $variable2)->value);
        
        // Check for null
        $variable3 = new QTIVariable('single', 'boolean');
        $this->assertNull(QTIVariable::or_($variable2, $variable3)->value);
    }
    
    public function testAnyN() {
        $variable1 = new QTIVariable('single', 'boolean', array('value' => true));
        $variable2 = new QTIVariable('single', 'boolean', array('value' => false));
        $this->assertTrue(QTIVariable::anyN(1, 3, $variable1, $variable1)->value);
        $this->assertFalse(QTIVariable::anyN(1, 3, $variable1, $variable1, $variable1, $variable1)->value);
        
        $variable3 = new QTIVariable('single', 'boolean');
        $this->assertNull(QTIVariable::anyN(2, 4, $variable1, $variable3, $variable3, $variable3)->value);

    }

    public function testMatch() {
        $variable1 = new QTIVariable('single', 'identifier', array('value' => 6));
        $this->assertTrue($variable1->match($variable1)->value);
        
        $variable2 = new QTIVariable('single', 'identifier', array('value' => 4));
        $this->assertFalse($variable1->match($variable2)->value);
        
        $variable3 = new QTIVariable('multiple', 'identifier', array('value' => array('A', 'B', 'C')));
        $this->assertTrue($variable3->match($variable3)->value);
        
        // Check for nulls
        $variable4 = new QTIVariable('single', 'identifier');
        $this->assertNull($variable4->match($variable3)->value);
    }
    
    public function testStringMatch() {
        $variable1 = new QTIVariable('single', 'string', array('value' => 'Some String'));
        $this->assertTrue($variable1->stringMatch($variable1, true)->value);
        $this->assertTrue($variable1->stringMatch($variable1, false)->value);
        
        $variable2 = new QTIVariable('single', 'string', array('value' => 'some string'));
        $this->assertTrue($variable1->stringMatch($variable1, false)->value);
        $this->assertFalse($variable1->stringMatch($variable2, true)->value);
        
        $variable3 = new QTIVariable('single', 'string');
        $this->assertNull($variable3->stringMatch($variable1, true)->value);
    }
    
    public function testPatternMatch() {
        $variable1 = new QTIVariable('single', 'string', array('value' => 'Some String'));
        $this->assertTrue($variable1->patternMatch('^Some')->value);
        $this->assertFalse($variable1->patternMatch('\d{3}')->value);
        $this->assertFalse($variable1->patternMatch('%\d')->value);
    }
    
    public function testEqual() {
        $variable1 = new QTIVariable('single', 'integer', array('value' => 56));
        $variable2 = new QTIVariable('single', 'integer', array('value' => 12));
        $this->assertTrue($variable1->equal($variable1)->value);
        $this->assertTrue($variable2->equal($variable2)->value);
        $this->assertFalse($variable1->equal($variable2)->value);
        $this->assertFalse($variable2->equal($variable1)->value);
        
        // absolute mode
        $this->assertFalse($variable1->equal($variable2, 'absolute', 5)->value);
        $this->assertTrue($variable1->equal($variable2, 'absolute', 50)->value);
        $this->assertTrue($variable1->equal($variable2, 'absolute', 44)->value);
        $this->assertFalse($variable1->equal($variable2, 'absolute', 44, false)->value);
        $this->assertTrue($variable2->equal($variable1, 'absolute', 44, false)->value);
        $this->assertTrue($variable2->equal($variable1, 'absolute', 44, false, true)->value);
        $this->assertFalse($variable2->equal($variable1, 'absolute', 44, false, false)->value);
        
        // relative mode
        $variable3 = new QTIVariable('single', 'integer', array('value' => 10));
        $variable4 = new QTIVariable('single', 'integer', array('value' => 5));
        $this->assertTrue($variable3->equal($variable4, 'relative', 50)->value);
        $this->assertFalse($variable3->equal($variable4, 'relative', 50, false)->value);
        $this->assertFalse($variable4->equal($variable3, 'relative', 50)->value);
        $this->assertFalse($variable4->equal($variable3, 'relative', 50, true, false)->value);
        
        // tolerance array
        $this->assertTrue($variable3->equal($variable4, 'relative', array(50, 0))->value);
        
    }
    
    public function testRoundTo() {
        $var1 = new QTIVariable('single', 'float');
        $var1->value = 3.175;
        $this->assertEquals(3.18, $var1->roundTo(3)->getValue());
    }
    
    /* 
     * This is the test from the spec.
     * */
      public function testEqualRounded() {
        // examples from spec
        $var1 = new QTIVariable('single', 'float');
        $var2 = new QTIVariable('single', 'float');
        $result1 = $var1->equalRounded($var2, 12);
        $this->assertNull($result1->value);
        $var1->value = 3.175;
        $var2->value = 3.183;
        $result2 = $var1->equalRounded($var2, 3);
        $this->assertTrue($result2->value);
        
        $var2->value = 3.1749;
        $result3 = $var1->equalRounded($var2, 3);
        $this->assertFalse($result3->value);

        $var1->value = 1.68572;
        $var2->value = 1.69;
        $result4 = $var1->equalRounded($var2, 2, 'decimalPlaces');
        $this->assertTrue($result4->value);
        
        $var2->value = 1.68432;
        $result5 = $var1->equalRounded($var2, 2, 'decimalPlaces');
        $this->assertFalse($result5->value);
    } 

    public function testLT() {
        $variable1 = new QTIVariable('single', 'integer', array('value' => 5));
        $variable2 = new QTIVariable('single', 'integer', array('value' => 300));
        $this->assertTrue($variable1->lt($variable2)->value);
        $this->assertFalse($variable2->lt($variable1)->value);
    }
    
    /* No tests for gt, lte, gte. Assume that any problems with these functions will
* also exist for lt */
    
    public function testSum() {
        $variable1 = new QTIVariable('single', 'integer', array('value' => 5));
        $this->assertEquals(15, QTIVariable::sum($variable1, $variable1, $variable1)->value);
    }
    
    public function testSubtract() {
        $variable1 = new QTIVariable('single', 'integer', array('value' => 5));
        $this->assertEquals(0, $variable1->subtract($variable1)->value);
        
        $variable2 = new QTIVariable('single', 'integer', array('value' => 2));
        $this->assertEquals(3, $variable1->subtract($variable2)->value);
        
    }
    
    public function testPower() {
        $variable1 = new QTIVariable('single', 'integer', array('value' => 5));
        $this->assertEquals(3125, $variable1->power($variable1)->value);
    
        $variable2 = new QTIVariable('single', 'integer', array('value' => 2));
        $this->assertEquals(25, $variable1->power($variable2)->value);
    
        $variable3 = new QTIVariable('single', 'float', array('value' => 25));
        $variable4 = new QTIVariable('single', 'float', array('value' => 0.5));
        $this->assertEquals(5, $variable3->power($variable4)->value);
        
    }
    
    public function testProduct() {
        $variable1 = new QTIVariable('single', 'integer', array('value' => 2));
        $variable2 = new QTIVariable('single', 'integer', array('value' => -16));
        $this->assertEquals(-32, QTIVariable::product($variable1, $variable2)->value);
    }
    
    public function testIntegerDivide() {
        $variable1 = new QTIVariable('single', 'integer', array('value' => 243));
        $variable2 = new QTIVariable('single', 'integer', array('value' => 6));
        $result1 = $variable1->integerDivide($variable2);
        $this->assertEquals('40', $result1->getValue());
        
        $variable3 = new QTIVariable('single', 'integer', array('value' => -243));
        $result2 = $variable3->integerDivide($variable2);
        $this->assertEquals('-41', $result2->getValue());
        
        // Check for null
        $variable4 = new QTIVariable('single', 'integer');
        $result3 = $variable3->integerDivide($variable4);
        $this->assertNull($result3->value);
    }

    public function testIntegerModulus() {
        $variable1 = new QTIVariable('single', 'integer', array('value' => 243));
        $this->assertEquals(0, $variable1->integerModulus($variable1)->value);
        
        $variable2 = new QTIVariable('single', 'integer', array('value' => 6));
        $result1 = $variable1->integerModulus($variable2);
        $this->assertEquals('3', $result1->getValue());
        
        $variable3 = new QTIVariable('single', 'integer', array('value' => -243));
        $result2 = $variable3->integerModulus($variable2);
        $this->assertEquals('-3', $result2->getValue());
        
        // Check for null
        $variable4 = new QTIVariable('single', 'integer');
        $result3 = $variable3->integerModulus($variable4);
        $this->assertNull($result3->value);
        
    }
    
    public function testTruncate() {
        $variable1 = new QTIVariable('single', 'float', array('value' => 6.8));
        $this->assertEquals(6, $variable1->truncate()->value);
        $this->assertEquals('integer', $variable1->truncate()->type);
        
        $variable2 = new QTIVariable('single', 'float', array('value' => -6.8));
        $this->assertEquals(-6, $variable2->truncate()->value);
        
    }
    
    public function testRound() {
        $variable1 = new QTIVariable('single', 'float', array('value' => 6.8));
        $this->assertEquals(7, $variable1->round()->value);
        $this->assertEquals('integer', $variable1->round()->type);
    
        $variable2 = new QTIVariable('single', 'float', array('value' => -6.5));
        $this->assertEquals(-6, $variable2->round()->value);
    
        $variable3 = new QTIVariable('single', 'float', array('value' => 6.49));
        $this->assertEquals(6, $variable3->round()->value);
        
    }
    
    public function testMapResponse() {
        $variable1 = new QTIVariable('single', 'identifier', array('value' => 'nim'));
        $mapping1 = new Mapping();
        $mapping1->defaultValue = 0;
        
        $mapEntry1 = new MapEntry();
        $mapEntry1->mapKey = 'nim';
        $mapEntry1->mappedValue = 5;
        $mapping1->addChild($mapEntry1);
        
        $mapEntry2 = new MapEntry();
        $mapEntry2->mapKey = 'bim';
        $mapEntry2->mappedValue = 365;
        $mapping1->addChild($mapEntry2);
        
        $variable1->mapping = $mapping1;
        
        $this->assertEquals(5, $variable1->mapResponse()->value);
        
        $variable2 = new QTIVariable('multiple', 'identifier', array('value' => array('nim', 'lim')));
        $mapping2 = new Mapping();
        $mapping2->defaultValue = 4;
        $mapping2->addChild($mapEntry1);
        $mapping2->addChild($mapEntry2);
        $variable2->mapping = $mapping2;
        
        $this->assertEquals(5, $variable2->mapResponse()->value);
        
        // Pair type
        $variable3 = new QTIVariable('multiple', 'pair', array('value' => array('A B', 'F G')));
        $mapping3 = new Mapping();
        $mapping3->defaultValue = 4;
        
        $mapEntry1 = new MapEntry();
        $mapEntry1->mapKey = 'A B';
        $mapEntry1->mappedValue = 5;
        $mapping3->addChild($mapEntry1);
        
        $mapEntry2 = new MapEntry();
        $mapEntry2->mapKey = 'C D';
        $mapEntry2->mappedValue = 365;
        $mapping3->addChild($mapEntry2);
        
        $variable3->mapping = $mapping3;
        $this->assertEquals(5, $variable3->mapResponse()->value);
        
        $variable3->value = array('B A', 'D E');
        $this->assertEquals(5, $variable3->mapResponse()->value);
        
        // defaultValue
        $variable4 = new QTIVariable('single', 'identifier', array('value' => 'nim'));
        $mapping4 = new Mapping();
        $mapping4->defaultValue = 654;
        
        $mapEntry1 = new MapEntry();
        $mapEntry1->mapKey = 'NIM';
        $mapEntry1->mappedValue = 5;
        $mapping4->addChild($mapEntry1);
        
        $mapEntry2 = new MapEntry();
        $mapEntry2->mapKey = 'bim';
        $mapEntry2->mappedValue = 365;
        $mapping4->addChild($mapEntry2);
        
        $variable4->mapping = $mapping4;
        
        $this->assertEquals(654, $variable4->mapResponse()->value);
        
        // caseSensitive (not implemented)
        // $mapEntry1->caseSensitive = 'false';
        // $this->assertEquals(5, $variable4->mapResponse()->value);
        
        
    }

    public function testMathConstant() {
        $result1 = QTIVariable::mathConstant('pi');
        $this->assertLessThan(3.15, $result1->value);
        $this->assertGreaterThan(3.13, $result1->value);
        
        $result2 = QTIVariable::mathConstant('e');
        $this->assertLessThan(2.72, $result2->value);
        $this->assertGreaterThan(2.70, $result2->value);
    }
    
    public function testMathOperator() {
        $var1 = new QTIVariable('single', 'integer', array('value' => 3));
        $result1 = QTIVariable::mathOperator('exp', array($var1));
        $this->assertEquals(exp(3), $result1->value);
    }
    
   /**
     * Test max function. Full coverage.
     */
    public function testMax() {
        $variable1 = new QTIVariable('single', 'integer', array('value' => 15));
        $variable2 = new QTIVariable('multiple', 'integer', array('value' => array(12, 17)));
        $result1 = QTIVariable::max($variable1, $variable2);
        $this->assertEquals('integer', $result1->type);
        $this->assertEquals('single', $result1->cardinality);
        $this->assertEquals(17, $result1->value);
        
        $variable3 = new QTIVariable('multiple', 'float', array('value' => array(12, 17)));
        $result2 = QTIVariable::max($variable1, $variable3);
        $this->assertEquals('float', $result2->type);
        $this->assertEquals('single', $result2->cardinality);
        $this->assertEquals(17, $result2->value);
        
        $variable4 = new QTIVariable('multiple', 'integer');
        $result3 = QTIVariable::max($variable1, $variable4);
        $this->assertEquals('float', $result3->type);
        $this->assertEquals('single', $result3->cardinality);
        $this->assertNull($result3->value);
        
        $variable5 = new QTIVariable('single', 'string', array('value' => 'hippopotamus'));
        $result4 = QTIVariable::max($variable1, $variable5);
        $this->assertEquals('float', $result4->type);
        $this->assertEquals('single', $result4->cardinality);
        $this->assertNull($result4->value);
        
    }
    
   /**
     * Test min function. Full coverage.
     */
    public function testMin() {
        $variable1 = new QTIVariable('single', 'integer', array('value' => 15));
        $variable2 = new QTIVariable('multiple', 'integer', array('value' => array(12, 17)));
        $result1 = QTIVariable::min($variable1, $variable2);
        $this->assertEquals('integer', $result1->type);
        $this->assertEquals('single', $result1->cardinality);
        $this->assertEquals(12, $result1->value);
        
        $variable3 = new QTIVariable('multiple', 'float', array('value' => array(-240, 17)));
        $result2 = QTIVariable::min($variable1, $variable3);
        $this->assertEquals('float', $result2->type);
        $this->assertEquals('single', $result2->cardinality);
        $this->assertEquals(-240, $result2->value);
        
        $variable4 = new QTIVariable('multiple', 'integer');
        $result3 = QTIVariable::min($variable1, $variable4);
        $this->assertEquals('float', $result3->type);
        $this->assertEquals('single', $result3->cardinality);
        $this->assertNull($result3->value);
        
        $variable5 = new QTIVariable('single', 'string', array('value' => 'hippopotamus'));
        $result4 = QTIVariable::min($variable1, $variable5);
        $this->assertEquals('float', $result4->type);
        $this->assertEquals('single', $result4->cardinality);
        $this->assertNull($result4->value);
        
    }
    
    public function testInside() {
        $variable1 = new QTIVariable('single', 'point', array('value' => '0 0'));
        
        // rectangle
        $this->assertTrue($variable1->inside('rect', '-1,1,1,-1')->value);
        $this->assertFalse($variable1->inside('rect', '1,2,2,1')->value);
        
        // circle
        $this->assertTrue($variable1->inside('circle', '0,0,25')->value);
        $this->assertTrue($variable1->inside('circle', '0,1,1')->value);
        $this->assertFalse($variable1->inside('circle', '0,1.1,1')->value);
        
        // circle2
        $variable2 = new QTIVariable('single', 'point', array('value' => '91 111'));
        $this->assertTrue($variable2->inside('circle', '102,113,16')->value);
        
        // poly
        //$this->assertTrue($variable1->inside('poly', '-1,-1,-1,1,1,1,1,-1')->value);
        
    }
    
    public function testFieldValue() {
    	$variable1 = new QTIVariable('record', null);
    	$variable1->setFieldValue('nim', new QTIVariable('single', 'identifier', array('value' => 'bim')));
    	$this->assertEquals('bim', $variable1->getFieldValue('nim')->value);
    }
    
    public function testGCD() {
    	$variable1 = new QTIVariable('single', 'integer', array('value' => 9));
    	$variable2 = new QTIVariable('single', 'integer', array('value' => 12));
    	$variable3 = QTIVariable::gcd(array($variable1, $variable2));
    	$this->assertEquals('3', $variable3->value);
    	$variable4 = new QTIVariable('single', 'integer', array('value' => 9));
    	$variable5 = QTIVariable::gcd(array($variable1, $variable2, $variable4));
    	$this->assertEquals('3', $variable3->value);
    	// Check for null
    	$variable6 = new QTIVariable('single', 'integer');
    	$result3 = QTIVariable::gcd(array($variable1, $variable2, $variable6));
    	$this->assertNull($variable6->value);
    	$this->assertNull($result3->value);
    	
    	// Check all zero
    	$variable7 = new QTIVariable('single', 'integer', array('value' => 0));
    	$this->assertEquals(0, QTIVariable::gcd(array($variable7, $variable7, $variable7, $variable7))->value);
    }
    
    public function testLCM() {
    	$variable1 = new QTIVariable('single', 'integer', array('value' => 9));
    	$variable2 = new QTIVariable('single', 'integer', array('value' => 12));
    	$variable3 = QTIVariable::lcm($variable1, $variable2);
    	$this->assertEquals('36', $variable3->value);
    	$variable4 = new QTIVariable('single', 'integer', array('value' => 9));
    	$variable5 = QTIVariable::lcm($variable1, $variable2, $variable4);
    	$this->assertEquals('36', $variable3->value);
    	// Check for null
    	$variable6 = new QTIVariable('single', 'integer');
    	$result3 = QTIVariable::lcm($variable1, $variable2, $variable6);
    	$this->assertNull($variable6->value);
    	$this->assertNull($result3->value);
    	 
    	// Check all zero
    	$variable7 = new QTIVariable('single', 'integer', array('value' => 0));
    	$this->assertEquals(0, QTIVariable::lcm($variable7, $variable7, $variable7, $variable7)->value);
    }
    
    public function testStatsOperator() {
        $var1 = new QTIVariable('single', 'integer', array('value' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)));
        $result1 = QTIVariable::statsOperator('mean', $var1);
        $this->assertEquals(5.5, $result1->value);
        $result2 = QTIVariable::statsOperator('popSD', $var1);
        $this->assertEquals(2.87228132327, $result2->value);
        $result3 = QTIVariable::statsOperator('popVariance', $var1);
        $this->assertEquals(8.25, $result3->value);
        $result4 = QTIVariable::statsOperator('sampleSD', $var1);
        $this->assertEquals(3.0276503541, $result4->value);
        $result5 = QTIVariable::statsOperator('sampleVariance', $var1);
        $this->assertEquals(9.16666666667, $result5->value);
    }
}


