<?php
 
namespace PHPQTI\Model;

use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\Inline;
use PHPQTI\Model\Base\InlineInteraction;

use PHPQTI\Util\ChoiceIterator;

class ChoiceInteraction extends \PHPQTI\Model\Gen\ChoiceInteraction 
    implements InlineInteraction, Flow, Inline, Interaction {

    protected $_elementName = 'choiceInteraction';

    public $id;
    public $class;
    public $label;
    public $responseIdentifier;
    public $shuffle;
    public $maxChoices;
    public $minChoices;
    public $orientation;
    
    /*
     * TODO: This is just for development. Rendering should maybe be done separately?
     */
    public function __invoke($controller) {
        $variableName = $this->responseIdentifier;
        // Add orientation (this is not used by any other code in the library,
        // but allows clients to use it if useful)
        if(isset($this->orientation)) {
            $orientationClass = 'qti_orientation_' . $orientation;
        } else {
            $orientationClass = '';
        }
        $result = "<div id=\"choiceInteraction_{$variableName}\" class=\"qti_blockInteraction {$this->cssClass()} $orientationClass\"";
        $result .= implode(' ', $this->_getDataAttributes(array('maxChoices', 'minChoices', 'orientation')));
        $result .= ">";
        // Work out what kind of HTML tag will be used for simpleChoices
        if (!isset($controller->response[$variableName])) {
            throw new \Exception("Declaration for $variableName not found");
        }
        
        $responseVariable = $controller->response[$variableName];
        $simpleChoiceType = 'radio';
        $brackets = ''; // we need brackets for multiple responses
        if ($responseVariable->cardinality == 'multiple') {
            $simpleChoiceType = 'checkbox';
            $brackets = '[]';
        }
        
        $this->simpleChoice = array();
        $this->fixed = array();
        $prompt = null;
        // Process child nodes
        foreach($this->_children as $child) {
            if ($child instanceof Prompt) {
                $prompt = $child;
            } else if ($child instanceof SimpleChoice) {
                $child->inputType = $simpleChoiceType;
                $child->name = $variableName.$brackets;
                $this->simpleChoice[] = $child;
                if(isset($child->fixed) && $child->fixed === 'true') {
                    $this->fixed[] = count($this->simpleChoice) - 1;
                }
            }
        }
        
        if (!is_null($prompt)) {
            $result .= $prompt->__invoke($controller);
        }
        
        $shuffle = $this->shuffle === 'true';
        $choiceIterator = new ChoiceIterator($this->simpleChoice, $shuffle);
        foreach($choiceIterator as $choice) {
            $result .= $choice->__invoke($controller, 'choiceInteraction');
        }
        
        $result .= "</div>";
        return $result;
    }
    
}