<?php

namespace PHPQTI\Runtime;

use PHPQTI\Runtime\Processing\Variable;

class ItemController {

    const STATE_NONE = 0;
    const STATE_INITIAL = 10;
    const STATE_INTERACTING = 20;
    const STATE_SUSPENDED = 30;
    const STATE_CLOSED = 40;
    const STATE_REVIEW = 50;
    const STATE_MODALFEEDBACK = 60;
    const STATE_SOLUTION = 70;

    public $state = ItemController::STATE_NONE;

    // A unique identifier for the controller.
    public $identifier;
    
    // Generated functions
    public $responseDeclaration = array();
    public $outcomeDeclaration = array();
    public $templateDeclaration = array();
    public $stylesheets = array(); // a simple array of stylesheets
    public $itemBody = array(); // there can be only one
    public $responseProcessing = array();
    public $templateProcessing = array();
    public $modalFeedback = array(); // The functions which determine which feedback to show
    
    public $modalFeedbackItems = array(); // The actual modal feedback HTML to be shown

    public $response = array();
    public $outcome = array();
    public $template = array();
    
    public $templateConditions = 0; // number of unsuccessful attempts to process templates

    public $response_source; // provides response values for variables
    public $persistence; // provides existing values of variables
    public $resource_provider; // provides URLs for images etc.

    public $show_debugging = false; // do we show memory usage etc.?

    /* The context is necessary for processing things like gaps in gapMatchInteraction
     * where the interaction itself is unable to directly control the creation of the 
     * child node, but needs to pass it some information.
     */
    public $context = array(); // for passing contextual info (e.g. ancestor nodes)

    public function __construct() {

    }

    public function setUpDefaultVars() {
        // Built-in variables (section 5.1.1 & 5.2.1 of info model)
        $this->response['numAttempts'] = new Variable('single', 'integer', array('value' => 0));
        $this->response['duration'] = new Variable('single', 'float', array('value' => 0));
        $this->outcome['completionStatus'] = new Variable('single', 'identifier', array('value' => 'not_attempted'));

        // TODO: We have this to get around mistakes (?) in the example QTI - should we?
        // (This is fixed in the final spec but should we leave it in?)
        $this->outcome['completion_status'] = $this->outcome['completionStatus'];
    }

    // TODO: We should be able to pass the form action URL to the controller
    // For example, if we want to remove one of the query string parameters before
    // posting back, or to post to a completely different script.
    public function showItemBody() {
        echo "<form method=\"post\" enctype=\"multipart/form-data\">";
        $resource_provider = $this->resource_provider;
        if(count($this->itemBody) > 0) {
        	echo $this->itemBody[0]($this);
        }
        echo "<input type=\"submit\" value=\"Submit response\"/>";
        echo "</form>";
    }

    // This just deals with the change of state and processing
    //    - the calling code should be responsible for calling showItemBody, and
    //      should also be responsible for displaying the results.
    // TODO: This is still just a demo workflow - it needs a bit of work!
    // We should really have buttons to explicitly end the session, suspend etc.
    public function run() {
        $this->persistence->restore($this);
        
        switch($this->state) {
            case ItemController::STATE_NONE:
                $this->beginItemSession();
                break;
            case ItemController::STATE_INITIAL:
                $this->beginAttempt();
                break;
            case ItemController::STATE_INTERACTING:
                if($this->response_source->isEndAttempt()) {
                    // TODO: fix (the person has submitted the item)
                    $this->endAttempt();
                } else {
                    $this->bindVariables();
                    $this->processResponse();
                }
        }       

        $this->persistence->persist($this);

    }

    public function beginItemSession() {
        $this->state = ItemController::STATE_INITIAL;
        $this->setUpDefaultVars();
        $this->beginAttempt();
    }

    public function endItemSession() {
        $this->state = ItemController::STATE_CLOSED;
    }

    public function beginAttempt() {
        $this->state = ItemController::STATE_INTERACTING;
        // 5.2.1 completionStatus set to unknown at start of first attempt
        if ($this->outcome['completionStatus']->value == 'not_attempted') {
            $this->outcome['completionStatus']->value = 'unknown';
        }
        // 5.1.1 numAttempts increases at the start of the attempt
        $this->response['numAttempts']->value++;
        
        // Initialise all the declared variables
        foreach($this->responseDeclaration as $key => $func) {
            $func($this);
        }
        
        foreach($this->outcomeDeclaration as $key => $func) {
            $func($this);
        }
        foreach($this->templateDeclaration as $key => $func) {
            $func($this);
        }
        
        // Initialise the outcome and template variables
        // TODO: Should we also do response variables? Should we even do templates?
        foreach($this->outcome as $name => $variable) {
            if (is_null($variable->value) && !is_null($variable->defaultValue)) {
                $this->outcome[$name]->value = $variable->defaultValue;
            }
        }
        
        foreach($this->template as $name => $variable) {
            if (is_null($variable->value) && !is_null($variable->defaultValue)) {
                $this->template[$name]->value = $variable->defaultValue;
            }
        }
        
        // Process templates
        foreach($this->templateProcessing as $func) {
            $func($this);
        }
    }
    
    /**
     * If a templateCondition returns true, we need to reset all template variables
     * and restart templateProcessing. We also need to make sure that we don't get an 
     * infinite loop.
     */
    public function doTemplateCondition() {
        if($this->templateConditions++ >= 100) {
            throw new \Exception("template condition maximum iterations exceeded");
        }
        
        foreach($this->templateDeclaration as $key => $func) {
            $func($this);
        }
        
        // Initialise the template variables
        foreach($this->template as $name => $variable) {
            if (is_null($variable->value) && !is_null($variable->defaultValue)) {
                $this->template[$name]->value = $variable->defaultValue;
            }
        }
        
        // Process templates
        foreach($this->templateProcessing as $func) {
            $func($this);
        }
    }

    public function endAttempt() {
        $this->bindVariables();
        $this->processResponse();
        // TODO: Shouldn't change state to closed here, but when should we??
        // $this->state = ItemController::STATE_CLOSED;
    }

    // Bind the responses to the controller variables
    public function bindVariables() {
        foreach($this->response as $key => $val) {
            $this->response_source->bindVariable($key, $val);
        }
    }

    public function setResponseSource(ResponseSource $response_source) {
        $this->response_source = $response_source;
    }

    public function setPersistence(Persistence $persistence) {
        $this->persistence = $persistence;
    }

    public function setResourceProvider(ResourceProvider $resource_provider) {
        $this->resource_provider = $resource_provider;
    }

    /**
     * Run the responseProcessing function which will update the outcome variables
     * based on the responses.
     */
    public function processResponse() {
        foreach($this->responseProcessing as $func) {
            $func($this);
        }

        // Reset the modal feedback
        $this->modalFeedbackItems = array();
        
        foreach($this->modalFeedback as $func) {
            $feedbackItem = $func($this);
            if (!empty($feedbackItem)) {
                $this->modalFeedbackItems[] = $feedbackItem;
            }
        }
    }

    // TODO: This should probably be the responsibility of the calling code
    public function displayVariables() {
        echo "<div class=\"well\">";
        foreach($this->outcome as $key => $outcome) {
            echo "$key: " . $outcome . "<br />";
        }
        echo "<hr />";
        foreach($this->response as $key => $response) {
            echo "$key: " . $response . "<br />";
        }
        echo "<hr />";
        foreach($this->template as $key => $template) {
            echo "$key: " . $template . "<br />";
        }

        echo "</div>";
    }
    
    public function displayDebugging() {
        if ($this->show_debugging) {
            echo '<div class="well"><hr />Memory: ' . memory_get_peak_usage() / (1024 * 1024) . "Mb</div>"; // TODO: Remove this debugging
        }
    }

    public function getCSS() {
        $result = '';
        if (count($this->stylesheets) == 0) {
            return $result;
        }
        foreach($this->stylesheets as $sheet) {
            $url = $this->resource_provider->urlFor($sheet);
            $result .= '<link rel="stylesheet" href="' . $url . "\"></link>\n";
        }
        return $result;
    }
    
    /**
     * Parse a *orVariableRef attribute.
     * 
     * If this is in the form {TEMPLATE_VAR} we return the variable, otherwise
     * we just return the value as-is. See 10.2 Using Template Variables in Operator
     * Attributes Values
     * 
     * TODO: This should check that the variable is declared as paramVariable or mathVariable
     * 
     * @param string $value
     */
    public function valueOrVariable($value) {
        $matches = array();
        if (preg_match('/^\{(\w*)\}$/', $value, $matches)) {
            if (isset($this->template[$matches[1]])) {
                return $this->template[$matches[1]]->value;
            } else if (isset($this->outcome[$matches[1]])) {
                return $this->outcome[$matches[1]]->value;
            } else {
                throw new \Exception("invalid template variable");
            }
        } else {
            return $value;
        }
    }
}