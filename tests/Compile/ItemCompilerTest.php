<?php

use PHPQTI\Runtime\Processing\Variable;

class ItemCompilerTest extends PHPUnit_Framework_TestCase {
    
    /**
     * Create a function from the given XML fragment.
     * 
     * @param string $xml
     */
    protected function toFunction($xml) {
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $el = $dom->documentElement;
        
        $compiler = new PHPQTI\Compile\ItemCompiler($dom);
        $rdcode1 = '$p = new PHPQTI\Runtime\FunctionGenerator(); return ' . $compiler->generating_function($el) . ';';
        //echo $rdcode1;
        return eval($rdcode1);
    }
	
	public function testResponseDeclaration() {
		$dom = new DOMDocument();
		$xml = '<responseDeclaration identifier="RESPONSE" cardinality="single" baseType="identifier">
		<correctResponse>
			<value>ChoiceA</value>
		</correctResponse>
	</responseDeclaration>';
		$rdfunc1 = $this->toFunction($xml);
		$controller1 = new PHPQTI\Runtime\ItemController();
		$rdfunc1($controller1);
		$this->assertInstanceOf('PHPQTI\Runtime\Processing\Variable', $controller1->response['RESPONSE']);
		$correctResponse1 = $controller1->response['RESPONSE']->getCorrectResponse();
		$this->assertInstanceOf('PHPQTI\Runtime\Processing\Variable', $correctResponse1);
		$this->assertEquals('ChoiceA', $correctResponse1->getValue());
	}
	
	public function testOutcomeDeclaration() {
		$xml = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="integer">
	<defaultValue>
	<value>0</value>
	</defaultValue>
	</outcomeDeclaration>';
		$rdfunc1 = $this->toFunction($xml);
		$controller1 = new PHPQTI\Runtime\ItemController();
		$rdfunc1($controller1);
		$this->assertInstanceOf('PHPQTI\Runtime\Processing\Variable', $controller1->outcome['SCORE']);
		$defaultValue1 = $controller1->outcome['SCORE']->getDefaultValue();
		$this->assertInstanceOf('PHPQTI\Runtime\Processing\Variable', $defaultValue1);
		$this->assertEquals(array('0'), $defaultValue1->getValue());
	}
	
	public function testResponseDeclarationWithMapping() {
	    $xml = '<responseDeclaration identifier="RESPONSE" cardinality="multiple" baseType="directedPair">
		<correctResponse>
			<value>C R</value>
			<value>D M</value>
			<value>L M</value>
			<value>P T</value>
		</correctResponse>
		<mapping defaultValue="0">
			<mapEntry mapKey="C R" mappedValue="1"/>
			<mapEntry mapKey="D M" mappedValue="0.5"/>
			<mapEntry mapKey="L M" mappedValue="0.5"/>
			<mapEntry mapKey="P T" mappedValue="1"/>
		</mapping>
	</responseDeclaration>';
	    $func1 = $this->toFunction($xml);
	    $controller1 = new PHPQTI\Runtime\ItemController();
		$func1($controller1);
		$this->assertInstanceOf('PHPQTI\Runtime\Processing\Variable', $controller1->response['RESPONSE']);
		$this->assertEquals('directedPair', $controller1->response['RESPONSE']->type);
		$this->assertNotNull($controller1->response['RESPONSE']->mapping);
		$this->assertEquals('1', $controller1->response['RESPONSE']->mapping->mapEntry['C R']);
		$this->assertEquals('0.5', $controller1->response['RESPONSE']->mapping->mapEntry['L M']);
		$this->assertEquals('0', $controller1->response['RESPONSE']->mapping->defaultValue);
	}
	
	public function testResponseDeclarationWithAreaMapping() {
	    $xml = '<responseDeclaration identifier="RESPONSE" cardinality="single" baseType="point">
	<correctResponse>
	<value>102 113</value>
	</correctResponse>
	<areaMapping defaultValue="0">
	<areaMapEntry shape="circle" coords="102,113,16" mappedValue="1"/>
	</areaMapping>
	</responseDeclaration>';
	    
	    $func1 = $this->toFunction($xml);
	    $controller1 = new PHPQTI\Runtime\ItemController();
	    $func1($controller1);
	    $r = $controller1->response['RESPONSE'];
	    $this->assertInstanceOf('PHPQTI\Runtime\Processing\Variable', $r);
	    $this->assertEquals('102,113,16', $r->areaMapping->areaMapEntries[0]->coords);
	}
	
	public function testResponseProcessing() {
	    $xml = '<?xml version="1.0" encoding="UTF-8"?>
<responseProcessing xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1 http://www.imsglobal.org/xsd/qti/qtiv2p1/imsqti_v2p1.xsd"> <!-- schemalocation fixed WK 26-01-2012 -->
    <responseCondition>
        <responseIf>
            <match>
                <variable identifier="RESPONSE"/>
                <correct identifier="RESPONSE"/>
            </match>
            <setOutcomeValue identifier="SCORE">
                <baseValue baseType="float">1</baseValue>
            </setOutcomeValue>
        </responseIf>
        <responseElse>
            <setOutcomeValue identifier="SCORE">
                <baseValue baseType="float">0</baseValue>
            </setOutcomeValue>
        </responseElse>
    </responseCondition>
</responseProcessing>';
	    $func1 = $this->toFunction($xml);
	    $controller1 = new PHPQTI\Runtime\ItemController();
	    $controller1->outcome['SCORE'] = new Variable('single', 'integer');
	    $controller1->response['RESPONSE'] = new Variable('single', 'integer', array('value' => 5, 'correctResponse' => 5));
	    $func1($controller1);
	    $this->assertEquals(1, $controller1->outcome['SCORE']->value);
	    $controller1->response['RESPONSE'] = new Variable('single', 'integer', array('value' => 6, 'correctResponse' => 5));
	    $func1($controller1);
	    $this->assertEquals(0, $controller1->outcome['SCORE']->value);
	}
}