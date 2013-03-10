<?php

namespace PHPQTI\Util;

/**
 * This class takes a QTI node and generates a PHP file containing an ObjectFactory
 * for a model of the node. 
 * 
 * It is intended to avoid having to create a DOM on each request.
 * 
 * @author Michael Aherne
 *
 */
class ObjectFactoryCompiler {

    public $dom;

    public function __construct($dom) {
        $this->dom = $dom;
    }

    public function generate_factory($classname, $namespace = null) {
        if (is_null($namespace)) {
            $namespacePrefix = 'namespace {';
            $namespaceSuffix = '}';
        } else {
            $namespacePrefix = 'namespace ' . $namespace . ';';
            $namespaceSuffix = '';
        }
        $result = "<?php $namespacePrefix\n\nclass {$classname} extends \PHPQTI\Util\ObjectFactory {\n
        public function __construct() {\n";
        
        // Create a function generator
        $result .= '$this->_instance = ' . $this->generating_function($this->dom->documentElement, '$this') . ';}}';
        
        $result .= $namespaceSuffix;
        
        return $result;
    }
    
    public function generating_function($node, $varname = '$p') {
        if (($node->nodeType == XML_COMMENT_NODE)) {
            return;
        }
        if (($node->nodeType == XML_CDATA_SECTION_NODE)) {
            if (trim($node->textContent) == '') {
                return;
            } else {
                return $varname . '->__text(\'' . $this->escapeSingleQuotes($node->textContent) . '\')';
            }
        }
        if (($node->nodeType == XML_TEXT_NODE)){
            if (trim($node->nodeValue) == '') {
                return;
            } else {
                return $varname . '->__text(\'' . $this->escapeSingleQuotes($node->nodeValue) . '\')';
            }
        }
    
        /*
         * Check the node's namespace URI. We could assume that namespaces
        * are set in the documentElement and not changed, which would simplify this
        * a bit, but it's not necessarily the case.
        */
        if (strpos($node->nodeName, ':') === false) {
            $methodName = $node->nodeName;
        } else {
            list($prefix, $name) = explode(':', $node->nodeName, 2);
            $nodeNamespace = $node->lookupNamespaceURI($prefix);
            switch ($nodeNamespace) {
                case 'http://www.imsglobal.org/xsd/imsqti_v2p1':
                    $methodName = $name;
                    break;
                case 'http://www.w3.org/1998/Math/MathML':
                    $methodName = '__mathml_' . $name;
                    break;
                default:
                    throw new Exception('Unsupported XML namespace: ' . $nodeNamespace);
            }
        }
    
        $result = $varname . '->' . $methodName . '(';
        $children = array();
        if (count($node->attributes) > 0) {
            $attrs = array();
            foreach($node->attributes as $attr) {
                $attrs[] = "'{$attr->name}' => '{$this->escapeSingleQuotes($attr->value)}'";
            }
            $children[] = 'array(' . implode(', ', $attrs) . ')';
        }
        if (!empty($node->childNodes)) {
            foreach($node->childNodes as $node) {
                $childFunction = $this->generating_function($node, $varname);
                if (!is_null($childFunction)) {
                    $children[] = $childFunction;
                }
            }
        }
        $result .= implode(",\n", $children);
        $result .= ')';
        return $result;
    }
    
    public function escapeSingleQuotes($string) {
        return str_replace("'", "\\'", $string);
    }
    
}