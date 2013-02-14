<?php

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

namespace PHPQTI\Compile;

// An attempt to generate PHP controller / view for a QTI item without XSLT
// This assumes that the XML is a valid QTI 2.1 item

class ItemCompiler {

    public $dom;

    public function __construct($dom) {
        $this->dom = $dom;
    }

    public function generate_controller($id) {
        $result = "<?php \nclass {$id}_controller extends PHPQTI\Runtime\ItemController {\n
        public function __construct() {\n";

        // Get things like title
        foreach($this->dom->documentElement->attributes as $attr) {
            if(in_array($attr->name, array('identifier', 'title', 'adaptive', 'timeDependent'))) {
                $result .= '$this->' . $attr->name . " = '" . addslashes($attr->value) . "';\n";
            }
        }

        // Create a function generator
        $result .= "\$f = new PHPQTI\Runtime\FunctionGenerator();\n";

        /*
         * The generated functions do different things:
         * 
         * responseDeclaration, outcomeDeclaration, templateDeclaration:
         * 
         * These all create functions which will initialise the given variable
         * when called.
         * 
         * templateProcessing:
         * 
         * Creates a function which will process the template variables
         * 
         * stylesheet:
         * 
         * Simply adds the stylesheet to the list of those that must be added to 
         * the head of any page displaying the item.
         * 
         * itemBody:
         * 
         * Creates a function which will render the item as HTML based on the values
         * of the variables at the time of calling.
         * 
         * responseProcessing:
         * 
         * Creates a function which processes the responses into outcomes.
         * 
         * modalFeedback:
         * 
         * Creates a function which returns an array of modal feedback HTML
         * which should be shown to the user.
         */
        // TODO: Check the namespace and ignore non-QTI
        foreach($this->dom->documentElement->childNodes as $child) {
        	$nodeName = $child->nodeName;
        	if ($nodeName == 'stylesheet') {
            	if (!is_null($attr = $stylesheetNode->attributes->getNamedItem('href'))) {
                    $result .= '$this->stylesheets[] = "' . $attr->value . "\";\n";
                }
        	}
        	
        	$functioncode = null;
        	if ($child->nodeName == 'responseProcessing' && !is_null($child->attributes->getNamedItem('template'))) {
        	    $template = $child->attributes->getNamedItem('template');
        	    if (strpos($template->value, "http://www.imsglobal.org/question/qti_v2p1/rptemplates/") === 0) {
        	        $template = str_replace("http://www.imsglobal.org/question/qti_v2p1/rptemplates/", '', $template->value);
        	        $dom = new \DOMDocument();
        	        $template_location = 'http://www.imsglobal.org/question/qti_v2p1/rptemplates/'.$template. '.xml';
        	        $dom->load($template_location);
        	        $functioncode = $this->generating_function($dom->documentElement, '$f');
        	    }
        	} else {
            	$functioncode = $this->generating_function($child, '$f');
        	}
        	
        	if (!is_null($functioncode)) {
        	    if (!is_null($attr = $child->attributes->getNamedItem('identifier'))) {
        	        $identifier = "'{$attr->value}'";
        	    } else {
        	        $identifier = '';
        	    }
        	    $result .= '$this->' . $nodeName . '[' . $identifier . '] = ';
        	    $result .= $functioncode;
        	    $result .= ";\n";
        	}
        }
        
        $result .= '}}';
        return $result;

    }

    // Return a view / responseProcessing generating function for a given XML node
    public function generating_function($node, $varname = '$p') {
        if (($node->nodeType == XML_COMMENT_NODE)) {
            return;
        }
        if (($node->nodeType == XML_CDATA_SECTION_NODE)) {
            if (trim($node->textContent) == '') {
                return;
            } else {
                return $varname . '->__text(\'' . addslashes($node->textContent) . '\')';
            }
        }
        if (($node->nodeType == XML_TEXT_NODE)){
            if (trim($node->nodeValue) == '') {
                return;
            } else {
                return $varname . '->__text(\'' . addslashes($node->nodeValue) . '\')';
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
                $attrs[] = "'{$attr->name}' => '{$attr->value}'";
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

    // Return a qti_variable constructor given a responseDeclaration or outcomeDeclaration node
    // TODO: This is kind of daft - why didn't I use the same function generation idea as the rest of the code??!!
    public function variable_declaration($node) {
        /* \$this->response['RESPONSE'] = new qti_variable('single', 'identifier', array(
         'correct' => 'ChoiceA'
        )); */
        $identifier = $node->getAttribute('identifier');
        $cardinality = $node->getAttribute('cardinality');
        $type = str_replace('Declaration', '', $node->nodeName);
        $result = '$this->' . $type . "['$identifier'] = new qti_variable('";
        $result .= $cardinality . "', '";
        $result .= $node->getAttribute('baseType') . "', array(";

        // Create params
        // TODO: Support things like "interpretation" attribute, record types etc.
        $params = array();
        foreach($node->childNodes as $child) {
            switch($child->nodeName) {
                case 'defaultValue':
                    $defaultValue = array();
                    foreach($child->childNodes as $valueNode) {
                        if ($valueNode->nodeType == XML_TEXT_NODE) {
                            continue;
                        }
                        $defaultValue[] = $valueNode->nodeValue;
                    }
                    if ($cardinality == 'single') {
                        $params[] = "'defaultValue' => '{$defaultValue[0]}'";
                    } else {
                        $params[] = "'defaultValue' => array('" . implode("','", $defaultValue) . "')";
                    }
                    break;
                case 'correctResponse':
                    $correctResponse = array();
                    foreach($child->childNodes as $valueNode) {
                        if ($valueNode->nodeType == XML_TEXT_NODE) {
                            continue;
                        }
                        $correctResponse[] = $valueNode->nodeValue;
                    }
                    if ($cardinality == 'single') {
                        $params[] = "'correctResponse' => '{$correctResponse[0]}'";
                    } else {
                        $params[] = "'correctResponse' => array('" . implode("','", $correctResponse) . "')";
                    }
                    break;
                case 'mapping':
                    $mapping = array();
                    foreach($child->attributes as $attr) {
                        $mapping[] = "'{$attr->name}' => '{$attr->value}'";
                    }

                    $mapEntry = array();
                    foreach($child->childNodes as $valueNode) {
                        if ($valueNode->nodeType == XML_TEXT_NODE) {
                            continue;
                        }
                        $mapEntry[] = "'{$valueNode->getAttribute('mapKey')}' => '{$valueNode->getAttribute('mappedValue')}'";
                    }

                    $mapping['mapEntry'] = "'mapEntry' => array(" . implode(",", $mapEntry) . ')';

                    $params[] = "'mapping' => array(" . implode(",", $mapping) . ')';
                    break;
                case 'areaMapping':
                    $areaMapping = array();
                    foreach($child->attributes as $attr) {
                        $areaMapping[] = "'{$attr->name}' => '{$attr->value}'";
                    }

                    $areaMapEntry = array();
                    foreach($child->childNodes as $valueNode) {
                        if ($valueNode->nodeType == XML_TEXT_NODE) {
                            continue;
                        }
                        $areaMapEntry[] = "array('shape' => '{$valueNode->getAttribute('shape')}',
                        'coords' => '{$valueNode->getAttribute('coords')}',
                        'mappedValue' => '{$valueNode->getAttribute('mappedValue')}')";
                    }

                    $areaMapping['areaMapEntry'] = "'areaMapEntry' => array(" . implode(",", $areaMapEntry) . ')';

                    $params[] = "'areaMapping' => array(" . implode(",", $areaMapping) . ')';
                    break;
            }
        }

        $result .= implode(',', $params);

        $result .= '));';
        return $result;
    }
}