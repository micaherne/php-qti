<?php

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

        foreach($this->dom->getElementsByTagNameNS('http://www.imsglobal.org/xsd/imsqti_v2p1', 'stylesheet') as $stylesheetNode) {
            if (!is_null($attr = $stylesheetNode->attributes->getNamedItem('href'))) {
                $result .= '$this->stylesheets[] = "' . $attr->value . "\";\n";
            }
        }
        
        // Create a function generator
        $result .= "\$f = new PHPQTI\Runtime\FunctionGenerator();\n";

        foreach($this->dom->documentElement->childNodes as $child) {
        	$nodeName = $child->nodeName;
        	if ($nodeName == 'stylesheet') {
        		continue;
        	}
        	
        	$functioncode = $this->generating_function($child, '$f');
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
        // Create the itemBody generator function
        //$result .= '$p = new qti_item_body($this);' . "\n";

        $itemBodyTags = $this->dom->getElementsByTagNameNS ('http://www.imsglobal.org/xsd/imsqti_v2p1', 'itemBody');
        foreach($itemBodyTags as $node) {
            $result .= $this->generating_function($node, '$p');
        }

        $result .= ";\n" . '$this->item_body = $p;' . "\n\n";

        // Create responseProcessing function
        $result .= '$r = new qti_response_processing($this);' . "\n";
        $responseProcessingTags = $this->dom->getElementsByTagNameNS ('http://www.imsglobal.org/xsd/imsqti_v2p1', 'responseProcessing');
        foreach($responseProcessingTags as $node) {
            // Check for template
            // TODO: template can be a URI, and templateLocation is used to find the XML
            // TODO: Remove hard coded location
            // TODO: Deal with other templates by downloading
            if (!is_null($node->attributes->getNamedItem('template'))) {
                $template = $node->attributes->getNamedItem('template');
                if (strpos($template->value, "http://www.imsglobal.org/question/qti_v2p1/rptemplates/") === 0) {
                    $template = str_replace("http://www.imsglobal.org/question/qti_v2p1/rptemplates/", '', $template->value);
                    $dom = new \DOMDocument();
                    $template_location = 'http://www.imsglobal.org/question/qti_v2p0/rptemplates/'.$template. '.xml';
                    $dom->load($template_location);
                    $result .= $this->generating_function($dom->documentElement, '$r');
                }
            } else {
                $result .= $this->generating_function($node, '$r');
            }
        }

        $result .= ";\n" . '$this->response_processing = $r;' . "\n";

        // Create templateProcessing function
        $result .= '$t = new qti_template_processing($this);' . "\n";
        $templateProcessingTags = $this->dom->getElementsByTagNameNS ('http://www.imsglobal.org/xsd/imsqti_v2p1', 'templateProcessing');
        foreach($templateProcessingTags as $node) {
            $result .= $this->generating_function($node, '$t');
        }

        $result .= ";\n" . '$this->template_processing = $t;' . "\n";

        // Create modalFeedback processor, and add modalFeedback processing functions
        $result .= '$m = new qti_modal_feedback_processing($this);' . "\n";
        $modalFeedbackTags = $this->dom->getElementsByTagNameNS ('http://www.imsglobal.org/xsd/imsqti_v2p1', 'modalFeedback');
        foreach($modalFeedbackTags as $node) {
            $result .= $this->generating_function($node, '$m');
            $result .= ";\n";
        }
        $result .= '$this->modal_feedback_processing = $m;' . "\n";


        // Close __construct
        $result .= "}";
        $result .= "    public function beginAttempt() {
                parent::beginAttempt();\n";

        foreach($this->dom->getElementsByTagNameNS ('http://www.imsglobal.org/xsd/imsqti_v2p1', 'responseDeclaration') as $responseDeclarationNode) {
            $result .= $this->variable_declaration($responseDeclarationNode);
        }

        foreach($this->dom->getElementsByTagNameNS ('http://www.imsglobal.org/xsd/imsqti_v2p1', 'outcomeDeclaration') as $outcomeDeclarationNode) {
            $result .= $this->variable_declaration($outcomeDeclarationNode);
        }

        foreach($this->dom->getElementsByTagNameNS ('http://www.imsglobal.org/xsd/imsqti_v2p1', 'templateDeclaration') as $templateDeclarationNode) {
            $result .= $this->variable_declaration($templateDeclarationNode);
        }

        $result .= "\$this->template_processing->execute();\n";

        // Close beginAttempt
        $result .= "}";
        // Close class
        $result .= "}";
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