<?php

use PHPQTI\Runtime\ItemController;
use PHPQTI\Runtime\Processing\Variable;
use PHPQTI\Runtime\FunctionGenerator;

//    PHP-QTI - a PHP library for QTI v2.1
//    Copyright (C) 2013 Michael Aherne
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program. If not, see <http://www.gnu.org/licenses/>.

/**
 * Test FunctionGenerator. 
 * 
 * NB: Many of these tests may break if we implement checking for the correct
 * QTI namespace.
 * 
 * @author Michael Aherne
 *
 */
class FunctionGeneratorTest extends PHPUnit_Framework_TestCase {
	
	public function testText() {
		$fg = new FunctionGenerator();
		$func = $fg->__text('hello');
		$result = $func(null);
		$this->assertEquals('hello', $result);
	}
	
	public function testFromXmlElement() {
		$fg = new FunctionGenerator();
		$dom = new \DomDocument();
		$dom->loadXML('<p xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1">Look at the text in the picture.</p>');
		$func = $fg->fromXmlElement($dom->documentElement);
		$result = $func(null);
		$this->assertEquals('<p>Look at the text in the picture.</p>', $result);
		
		$dom->loadXML('<responseDeclaration identifier="RESPONSE" cardinality="single" baseType="identifier">
		<correctResponse interpretation="Choice A">
			<value>ChoiceA</value>
		</correctResponse>
	</responseDeclaration>');
		$func = $fg->fromXmlElement($dom->documentElement);
		$controller = new ItemController();
		$result = $func($controller);
		$var = $controller->response['RESPONSE'];
		$this->assertEquals('ChoiceA', $var->correctResponse);
		$this->assertEquals('Choice A', $var->correctResponseInterpretation);
	}
	
	public function testOutcomeDeclaration() {
        $fg = new FunctionGenerator();
		$dom = new \DomDocument();
		$dom->loadXML('<outcomeDeclaration identifier="STORY" cardinality="single" baseType="identifier">
		<defaultValue>
			<value>openingGambit</value>
		</defaultValue>
	</outcomeDeclaration>');
		
		$func = $fg->fromXmlElement($dom->documentElement);
		$controller = new ItemController();
		$result = $func($controller);
		$var = $controller->outcome['STORY'];
		$this->assertEquals('openingGambit', $var->defaultValue);
	}

public function testResponseDeclaration() {
	    $fg = new FunctionGenerator();
	    $dom = new \DomDocument();
	    $dom->loadXML('<responseDeclaration identifier="STORY" cardinality="multiple" baseType="identifier">
		<defaultValue interpretation="The Beatles">
			<value>john</value>
			<value>paul</value>
	        <value>george</value>
	        <value>ringo</value>
	    </defaultValue>
	</responseDeclaration>');
	
	    $func = $fg->fromXmlElement($dom->documentElement);
	    $controller = new ItemController();
	    $result = $func($controller);
	    $var = $controller->response['STORY'];
	    $this->assertInternalType('array', $var->defaultValue);
	    $this->assertEquals(array('john', 'paul', 'george', 'ringo'), $var->defaultValue);
	    $this->assertEquals('The Beatles', $var->defaultValueInterpretation);
	}
	
    public function testTemplateDeclaration() {
	    $fg = new FunctionGenerator();
	    $dom = new \DomDocument();
	    $dom->loadXML('<templateDeclaration baseType="float" cardinality="single" identifier="fAns" mathVariable="true"
        paramVariable="false"/>');
	
	    $func = $fg->fromXmlElement($dom->documentElement);
	    $controller = new ItemController();
	    $result = $func($controller);
	    $var = $controller->template['fAns'];
	    $this->assertEquals(false, $var->paramVariable);
	    $this->assertEquals(true, $var->mathVariable);
    }
    
    public function testMathOperator() {
        $xml = '<mathOperator name="exp">
                <baseValue baseType="integer">3</baseValue>
            </mathOperator>';
        $fg = new FunctionGenerator();
        $func = $fg->fromXmlString($xml);
        $result1 = $func(null);
        //$this->assertEquals(exp(3), $result1->value);
    }
    
    public function testTemplateProcessing() {
        $xml = '    <templateProcessing>
        <setTemplateValue identifier="iA">
            <randomInteger max="4" min="1"/>
        </setTemplateValue>
        <setTemplateValue identifier="fAns">
            <mathOperator name="exp">
                <variable identifier="iA"/>
            </mathOperator>
        </setTemplateValue>
        <setTemplateValue identifier="fR">
            <roundTo figures="3" roundingMode="decimalPlaces">
                <variable identifier="fAns"/>
            </roundTo>
        </setTemplateValue>
    </templateProcessing>';
        $fg = new FunctionGenerator();
		$func = $fg->fromXmlString($xml);
        $controller = new ItemController();
        $controller->template['iA'] = new Variable('single', 'integer');
        $controller->template['fAns'] = new Variable('single', 'float');
        $controller->template['fR'] = new Variable('single', 'float');
        $func($controller);
        $this->assertNotNull($controller->template['iA']->value);
        $this->assertNotNull($controller->template['fAns']->value);
        $this->assertNotNull($controller->template['fR']->value);
    }
	
	public function testVariable() {
	    $xml = '<variable identifier="FEEDBACK"/>';
	    $fg = new FunctionGenerator();
	    $dom = new \DomDocument();
	    $dom->loadXML($xml);
	     
	    $func = $fg->fromXmlElement($dom->documentElement);
	    $controller = new ItemController();
	    $controller->outcome['FEEDBACK'] = new Variable('multiple', 'identifier', array('value' => array('before', 'again')));
	    $this->assertEquals('multiple', $controller->outcome['FEEDBACK']->cardinality);
	    $result = $func($controller);
	    $this->assertEquals('multiple', $result->cardinality);
	}
	
	public function testDelete() {
	    $xml = '<delete>
						<variable identifier="FEEDBACK"/>
						<baseValue baseType="identifier">again</baseValue>
					</delete>';
	    $fg = new FunctionGenerator();
	    $dom = new \DomDocument();
	    $dom->loadXML($xml);
	    
	    $func = $fg->fromXmlElement($dom->documentElement);
	    $controller = new ItemController();
	    $controller->outcome['FEEDBACK'] = new Variable('multiple', 'identifier', array('value' => array('before', 'again')));
	    $this->assertEquals('multiple', $controller->outcome['FEEDBACK']->cardinality);
	    //$result = $func($controller);
	    //$this->assertEquals(array('before'), $controller->outcome['FEEDBACK']->value);
	}
	
	public function testIndex() {
	    $fg = new FunctionGenerator();
	    $dom = new \DomDocument();
	    
	    $xml = '<index n="2"><variable identifier="numbers"/></index>';
	    $dom->loadXML($xml);
	     
	    $func = $fg->fromXmlElement($dom->documentElement);
	    
	    $controller = new ItemController();
	    $controller->response['numbers'] = new Variable('multiple', 'integer', array('value' => array(1, 2, 3, 4, 5, 6)));
	    $controller->template['i'] = new Variable('single', 'integer', array('value' => 5));
	    $result1 = $func($controller);
	    $this->assertInstanceOf('PHPQTI\Runtime\Processing\Variable', $result1);
	    $this->assertEquals('2', $result1->value);
	    
	    // Test template variable substitution
	    $xml = '<index n="{i}"><variable identifier="numbers"/></index>';
	    $dom->loadXML($xml);
	    $func = $fg->fromXmlElement($dom->documentElement);
	    $result2 = $func($controller);
	    $this->assertEquals('5', $result2->value);
	     
	    
	}
	
    function testExitResponse() {
		$xml = '<responseProcessing>
				<setOutcomeValue identifier="SCORE">
                <baseValue baseType="identifier">before</baseValue>
            </setOutcomeValue>
				<exitResponse/>
				<setOutcomeValue identifier="SCORE">
                <baseValue baseType="identifier">after</baseValue>
            </setOutcomeValue>
				</responseProcessing>';

		$fg = new FunctionGenerator();
		$dom = new \DomDocument();
		$dom->loadXML($xml);
		 
		$func = $fg->fromXmlElement($dom->documentElement);
		$controller = new ItemController();
		$controller->outcome['SCORE'] = new Variable('single', 'identifier', array('value' => 'initial'));
		$func($controller);
		$this->assertEquals('before', $controller->outcome['SCORE']->value);
	}
	
	function testExitTemplate() {
		$xml = '<templateProcessing>
				<setTemplateValue identifier="SCORE">
                <baseValue baseType="identifier">before</baseValue>
            </setTemplateValue>
				<exitTemplate/>
				<setTemplateValue identifier="SCORE">
                <baseValue baseType="identifier">after</baseValue>
            </setTemplateValue>
				</templateProcessing>';
	
		$fg = new FunctionGenerator();
		$dom = new \DomDocument();
		$dom->loadXML($xml);
			
		$func = $fg->fromXmlElement($dom->documentElement);
		$controller = new ItemController();
		$controller->template['SCORE'] = new Variable('single', 'identifier', array('value' => 'initial'));
		$func($controller);
		$this->assertEquals('before', $controller->template['SCORE']->value);
	}
	
	function testRandomInteger() {
	    $xml = '<randomInteger max="4" min="1"/>';
	    $fg = new FunctionGenerator();
	    $dom = new \DomDocument();
	    $dom->loadXML($xml);
	    	
	    $func = $fg->fromXmlElement($dom->documentElement);
	    $controller = new ItemController();
	    
	    // Run it ten times
	    for($i = 0; $i < 10; $i++) {
	        $result1 = $func($controller);
	        $this->assertGreaterThanOrEqual(1, $result1->value);
	        $this->assertLessThanOrEqual(4, $result1->value);
	        $results1[$result1->value] = 1;
	    }
	    
	    $xml = '<randomInteger max="{TEST}" min="17"/>';
	    $dom->loadXML($xml);
	    $func = $fg->fromXmlElement($dom->documentElement);
	     
	    $controller->template['TEST'] = new Variable('single', 'integer', array('value' => 17));
	    $result2 = $func($controller);
	    $this->assertEquals(17, $result2->value);
	}
	
}