<?php

use PHPQTI\Runtime\QTIVariable;

use PHPQTI\Model\AssessmentItem;
use PHPQTI\Util\XMLUtils;
use PHPQTI\Runtime\AssessmentItemController;

class ModelTest extends PHPUnit_Framework_TestCase {
    
    public function fromXmlFragment($xml) {
        $dom = new \DomDocument();
        $this->assertTrue($dom->loadXML($xml));
        $x = new XMLUtils();
        $result = $x->unmarshall($dom, true);
        $this->assertNotNull($result);
        return $result;
    }
    
    public function testBaseValue() {
        $baseValue = $this->fromXmlFragment('<baseValue baseType="float">1</baseValue>');
        
        $result = $baseValue(null);
        $this->assertEquals('float', $result->type);
        $this->assertEquals(1, $result->value);
        
        // Test boolean converted correctly
        $baseValue = $this->fromXmlFragment('<baseValue baseType="boolean">false</baseValue>');
        $result2 = $baseValue(null);
        $this->assertEquals('boolean', $result2->type);
        $this->assertFalse($result2->value);
        
    }
    
    public function testCorrect() {
        $correct = $this->fromXmlFragment('<correct identifier="RESPONSE"/>');
        
        $controller->response['RESPONSE'] = new QTIVariable('single', 'identifier', array('value' => 'testval', 'correctResponse' => 'correctvalue'));
        $result = $correct($controller);
        $this->assertEquals('correctvalue', $result->value);
    }
    
    public function testDefaultValue() {
        $defaultValue = $this->fromXmlFragment('<defaultValue interpretation="The Beatles">
    			<value>john</value>
    			<value>paul</value>
    	        <value>george</value>
    	        <value>ringo</value>
    	    </defaultValue>');
        
        $this->assertCount(4, $defaultValue->getChildren());
        $item = new AssessmentItem();
        $controller = new AssessmentItemController($item);
        $result = $defaultValue($controller);
        $this->assertInternalType('array', $result);
        $this->assertEquals(array('john', 'paul', 'george', 'ringo'), $result);
        $this->assertEquals('The Beatles', $defaultValue->interpretation);
        
    }
    
    public function testDivide() {
        $divide = $this->fromXmlFragment('<divide>
                <mathConstant name="pi"/>
                <baseValue baseType="integer">180</baseValue>
            </divide>');
        
        $result = $divide(null);
        $this->assertEquals(pi()/180, $result->value);
    }
    
    public function testEqual() {
        $equal = $this->fromXmlFragment('<equal toleranceMode="exact">
                    <variable identifier="iA"/>
                    <variable identifier="iB"/>
                </equal>');

        $item = new AssessmentItem();
        $controller = new AssessmentItemController($item);
        $controller->template['iA'] = new QTIVariable('single', 'integer', array('value' => 50));
        $controller->template['iB'] = $controller->template['iA'];
        $result = $equal($controller);
        $this->assertTrue($result->value);
        
        $controller->template['iB'] = new  QTIVariable('single', 'integer', array('value' => 51));
        $result = $equal($controller);
        $this->assertFalse($result->value); 
    }
    
    public function testMatch() {
        $match = $this->fromXmlFragment('<match>
                <variable identifier="RESPONSE"/>
                <correct identifier="RESPONSE"/>
            </match>');
        
        $item = new AssessmentItem();
        $controller = new AssessmentItemController($item);
        $controller->response['RESPONSE'] = new QTIVariable('single', 'identifier', array('value' => 'aardvark', 'correctResponse' => 'aardvark'));
        $result1 = $match($controller);
        $this->assertTrue($result1->value);
        
        $controller->response['RESPONSE'] = new QTIVariable('single', 'identifier', array('value' => 'aardvark', 'correctResponse' => 'aardwolf'));
        $result2 = $match($controller);
        $this->assertFalse($result2->value);
        
    }
    
    public function testMapping() {
        $mapping = $this->fromXmlFragment('<mapping lowerBound="0" upperBound="2" defaultValue="-2">
			<mapEntry mapKey="H" mappedValue="1"/>
			<mapEntry mapKey="O" mappedValue="1"/>
			<mapEntry mapKey="Cl" mappedValue="-1"/>
		</mapping>');
        
        $item = new AssessmentItem();
        $controller = new AssessmentItemController($item);
        
        $result1 = $mapping($controller);
        $this->assertCount(3, $result1->getChildren('PHPQTI\Model\MapEntry'));
    }

    public function testMax() {
        $max = $this->fromXmlFragment('<max><variable identifier="t"/></max>');
        $item = new AssessmentItem();
        $controller = new AssessmentItemController($item);
        $controller->template['t'] = new QTIVariable('ordered', 'integer', array('value' => array(2, 4, 6, 8)));
        $result1 = $max($controller);
        $this->assertEquals(8, $result1->value);
    }
    
    public function testMin() {
        $min = $this->fromXmlFragment('<min><variable identifier="t"/></min>');
        $item = new AssessmentItem();
        $controller = new AssessmentItemController($item);
        $controller->template['t'] = new QTIVariable('ordered', 'integer', array('value' => array(2, 4, 6, 8)));
        $result1 = $min($controller);
        $this->assertEquals(2, $result1->value);
    }
    
    public function testNot() {
        $not = $this->fromXmlFragment('<not><baseValue baseType="boolean">true</baseValue></not>');
        $result1 = $not(null);
        $this->assertFalse($result1->value);
        
        $not = $this->fromXmlFragment('<not><baseValue baseType="boolean">false</baseValue></not>');
        $result2 = $not(null);
        $this->assertTrue($result2->value);
    }
    
    public function testRandomInteger() {
        $randomInteger = $this->fromXmlFragment('<randomInteger min="50" max="85" step="5"/>');
        $item = new AssessmentItem();
        $controller = new AssessmentItemController($item);
        // do it several times
        for ($i = 0; $i < 20; $i++) {
            $result1 = $randomInteger($controller);
            $this->assertGreaterThanOrEqual(50, $result1->value);
            $this->assertLessThanOrEqual(85, $result1->value);
            $this->assertEquals(0, $result1->value % 5);
        }
    }
    
    public function testResponseDeclaration() {
        $responseDeclaration = $this->fromXmlFragment('<responseDeclaration identifier="STORY" cardinality="multiple" baseType="identifier">
        		<defaultValue interpretation="The Beatles">
        			<value>john</value>
        			<value>paul</value>
        	        <value>george</value>
        	        <value>ringo</value>
        	    </defaultValue>
        	</responseDeclaration>');

        $item = new AssessmentItem();
        $controller = new AssessmentItemController($item);
        $responseDeclaration($controller);
        $story = $controller->response['STORY'];
        $this->assertInternalType('array', $story->defaultValue);
        
        $responseDeclaration2 = $this->fromXmlFragment('<responseDeclaration identifier="RESPONSE_P" cardinality="single" baseType="integer"/>');
        $controller2 = new AssessmentItemController($item);
        $responseDeclaration2($controller2);
        $this->assertArrayHasKey('RESPONSE_P', $controller2->response);
    }
    
    public function testSetOutcomeValue() {
        $setOutcomeValue = $this->fromXmlFragment('<setOutcomeValue identifier="SCORE">
                <baseValue baseType="float">1</baseValue>
            </setOutcomeValue>');
        
        $item = new AssessmentItem();
        $controller = new AssessmentItemController($item);
        $setOutcomeValue($controller);
        $story = $controller->outcome['SCORE'];
        
        $this->assertEquals('float', $story->type);
        $this->assertEquals(1, $story->value);
    }
    
    public function testValue() {
        $value = $this->fromXmlFragment('<value>ringo</value>');

        $controller = new AssessmentItemController();
        $result = $value($controller);
        $this->assertEquals('ringo', $result);
    }
    
    public function testVariable() {
        $variable = $this->fromXmlFragment('<variable identifier="RESPONSE"/>');
        
        $controller = new AssessmentItemController();
        $controller->response['RESPONSE'] = new QTIVariable('single', 'identifier', array('value' => 'testval'));
        $result = $variable($controller);
        $this->assertEquals('testval', $result->value);
        
        // template variable
        $variable = $this->fromXmlFragment('<variable identifier="t"/>');
        $controller->template['t'] = new QTIVariable('ordered', 'integer', array('value' => array(2, 4, 6)));
        $result2 = $variable($controller);
        $this->assertEquals(4, $result2->value[1]);
    }
    
}