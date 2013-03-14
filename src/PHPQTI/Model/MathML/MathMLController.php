<?php

namespace PHPQTI\Model\MathML;

use PHPQTI\Model\Base\AbstractClass;

class MathMLController extends AbstractClass {
        
    protected $xml;
    protected $dom; // A DOM document created from the XML
    
    public function __construct($xml) {
        $this->xml = $xml;
    }
    
    public function __invoke($controller) {
        $this->dom = new \DOMDocument();
        $this->dom->loadXML($this->xml);
        
        // Do template variable substitution
        foreach($controller->template as $name => $var) {
            if ($var->mathVariable) {
                $mi = $this->dom->getElementsByTagNameNS('http://www.w3.org/1998/Math/MathML', 'mi');
                foreach($mi as $identifierNode) {
                    if ($identifierNode->nodeValue == $name) {
                        $mn = $this->dom->createElementNS('http://www.w3.org/1998/Math/MathML', 'mn', $var->value);
                        $identifierNode->parentNode->replaceChild($mn, $identifierNode);
                    }
                }
                $ci = $this->dom->getElementsByTagNameNS('http://www.w3.org/1998/Math/MathML', 'ci');
                foreach($ci as $identifierNode) {
                    if ($identifierNode->nodeValue == $name) {
                        $mn = $this->dom->createElementNS('http://www.w3.org/1998/Math/MathML', 'cn', $var->value);
                        $identifierNode->parentNode->replaceChild($mn, $identifierNode);
                    }
                }
            }
        }
        
        return $this->dom->saveXML();
        
        
    }
}