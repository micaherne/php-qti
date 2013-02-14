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
		<correctResponse>
			<value>ChoiceA</value>
		</correctResponse>
	</responseDeclaration>');
		$func = $fg->fromXmlElement($dom->documentElement);
		$controller = new ItemController();
		$result = $func($controller);
		$var = $controller->response['RESPONSE'];
		$this->assertEquals('ChoiceA', $var->correctResponse);
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
	
}