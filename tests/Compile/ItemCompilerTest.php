<?php

class ItemCompilerTest extends PHPUnit_Framework_TestCase {
	
	public function testResponseDeclaration() {
		$dom = new DOMDocument();
		$xml = '<responseDeclaration identifier="RESPONSE" cardinality="single" baseType="identifier">
		<correctResponse>
			<value>ChoiceA</value>
		</correctResponse>
	</responseDeclaration>';
		$dom->loadXML($xml);
		$el = $dom->documentElement;
		$this->assertEquals('responseDeclaration', $el->nodeName);
		
		$compiler = new PHPQTI\Compile\ItemCompiler($dom);
		$rdcode1 = '$p = new PHPQTI\Runtime\FunctionGenerator(); return ' . $compiler->generating_function($el) . ';';
		$rdfunc1 = eval($rdcode1);
		$controller1 = new PHPQTI\Runtime\ItemController();
		$rdfunc1($controller1);
		$this->assertInstanceOf('PHPQTI\Runtime\Processing\Variable', $controller1->response['RESPONSE']);
		$correctResponse1 = $controller1->response['RESPONSE']->getCorrectResponse();
		$this->assertInstanceOf('PHPQTI\Runtime\Processing\Variable', $correctResponse1);
		$this->assertEquals(array('ChoiceA'), $correctResponse1->getValue());
	}
	
	public function testOutcomeDeclaration() {
		$dom = new DOMDocument();
		$xml = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="integer">
	<defaultValue>
	<value>0</value>
	</defaultValue>
	</outcomeDeclaration>';
		$dom->loadXML($xml);
		$el = $dom->documentElement;
		$this->assertEquals('outcomeDeclaration', $el->nodeName);
	
		$compiler = new PHPQTI\Compile\ItemCompiler($dom);
		$rdcode1 = '$p = new PHPQTI\Runtime\FunctionGenerator(); return ' . $compiler->generating_function($el) . ';';
		$rdfunc1 = eval($rdcode1);
		$controller1 = new PHPQTI\Runtime\ItemController();
		$rdfunc1($controller1);
		$this->assertInstanceOf('PHPQTI\Runtime\Processing\Variable', $controller1->outcome['SCORE']);
		$defaultValue1 = $controller1->outcome['SCORE']->getDefaultValue();
		$this->assertInstanceOf('PHPQTI\Runtime\Processing\Variable', $defaultValue1);
		$this->assertEquals(array('0'), $defaultValue1->getValue());
	}
	
	
	
}