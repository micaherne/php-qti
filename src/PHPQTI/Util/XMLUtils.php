<?php

namespace PHPQTI\Util;

/**
 * A very basic XML unmarshalling class to convert a QTI element node into a 
 * tree of objects.
 * 
 * @author michael
 *
 */
class XMLUtils {
    
    private static $reservedNames = array('__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor');
    const NAMESPACE_QTI21_FINAL = 'http://www.imsglobal.org/xsd/imsqti_v2p1';
    
    public static function className($elementName) {
        if (in_array($elementName, XMLUtils::$reservedNames)) {
            return 'QTI' . ucfirst($elementName);
        } else {
            return ucfirst($elementName);
        }
    }
    
    /**
     * Given an XML document DOM, return the model object represented by it.
     * 
     * @param \DOMDocument $dom the document DOM
     * @param string $assumeQTI21namespace assume the dom node is a fragment without namespace declaration (for testing) - default false
     * @return object the corresponding model object
     */
    public function unmarshall(\DOMDocument $dom, $assumeQTI21namespace = false) {
        return $this->unmarshallNode($dom->documentElement, $assumeQTI21namespace);
    }
    
    private function unmarshallNode(\DOMNode $node, $assumeQTI21namespace = false, $ignoreWhitespace = true) {
        // echo "Calling unmarshallNode: " . $node->nodeName . "\n";
        $result = null;
        if ($node->nodeName == 'itemBody') {
        	$ignoreWhitespace = false;
        }
        switch ($node->nodeType) {
            case XML_TEXT_NODE:
                if ($ignoreWhitespace && trim($node->nodeValue) == '') {
                    return;
                } else {
                    return new \PHPQTI\Model\Base\Text($node->nodeValue);
                }
                break;
            case XML_ELEMENT_NODE:
                if ($assumeQTI21namespace || $node->namespaceURI == XMLUtils::NAMESPACE_QTI21_FINAL) {
                    $classname = 'PHPQTI\\Model\\' . XMLUtils::className($node->nodeName);
                    if (class_exists($classname)) {
                        $result = new $classname();
                        foreach($node->attributes as $name => $attributeNode) {
                            //echo "$name\n";
                            $prefix = null; // must be null for lookupNamespaceURI - see docs
                            $namespace = null;
                            $nodeName = '';
                            if (strpos($attributeNode->nodeName, ':') === false) {
                                $nodeName = $attributeNode->nodeName;
                            } else {
                                list($prefix, $nodeName) = explode(':', $attributeNode->nodeName, 2);
                            }
                            $namespace = $attributeNode->lookupNamespaceURI($prefix);
                            if ($assumeQTI21namespace || $namespace == XMLUtils::NAMESPACE_QTI21_FINAL) {
                            if (property_exists($classname, $nodeName)) {
                                    $result->$nodeName = $attributeNode->nodeValue;
                                } else {
                                    //echo "WTF kind of property is $nodeName\n";
                                }
                            } else {
                                // echo "WTF kind of attribute is this: " . $attributeNode->nodeName;
                            }
                        }
                        
                        foreach($node->childNodes as $child) {
                            $c = $this->unmarshallNode($child, $assumeQTI21namespace, $ignoreWhitespace);
                            if (!is_null($c)) {
                                //echo "Adding child " . $child->nodeName . "\n";
                                $result->addChild($c);
                            } else {
                                //echo "WTF kind of child is " . $child->nodeName . "?\n";
                            }
                        }
                    }
                } else {
                    //echo "WTF kind of namespace is that?\n";
                }
                break;
            default:
                return;
                break;
        }
        
        return $result;
    }

}