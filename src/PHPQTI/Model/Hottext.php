<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Runtime\QTIVariable;

use PHPQTI\Model\Base\Choice;

class Hottext extends \PHPQTI\Model\Gen\Hottext implements Choice {

    protected $_elementName = 'hottext';

    public function __invoke($controller) {
        $result = "<span class=\"qti_hottext\">\n";
    
        $hottextInteraction = $this->findAncestorWithInterface('PHPQTI\Model\Base\Interaction');
    
        $variable = $controller->response[$hottextInteraction->responseIdentifier];
        
        $testvar = new QTIVariable('single', $variable->type, array('value' => $this->identifier));
        if ($variable->cardinality == 'multiple') {
            $hottextType = 'checkbox';
            $brackets = '[]';
            $comparisonresult = $variable->contains($testvar);
        } else {
            $hottextType = 'radio';
            $brackets = '';
            $comparisonresult = $variable->match($testvar);
        }
        $checked = $comparisonresult->value ? " checked=\"checked\" " : "";
        $result .= "<input type=\"{$hottextType}\" name=\"{$hottextInteraction->responseIdentifier}{$brackets}\" value=\"{$this->identifier}\" {$checked}/> ";
    
        foreach($this->_children as $child) {
            $result .= $child($controller);
        }
        $result .= "</span>";
        return $result;
    }
    
    
}