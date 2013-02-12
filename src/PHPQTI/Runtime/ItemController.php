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

    public $response = array();
    public $outcome = array();
    public $template = array();

    public $response_source; // provides response values for variables
    public $persistence; // provides existing values of variables
    public $resource_provider; // provides URLs for images etc.

    public $response_processing; // closure which processes responses
    public $item_body; // closure which displays item body
    public $modal_feedback_processing; // closure which displays modal feedback

    public $stylesheets; // a simple array of stylesheets

    public $show_debugging = false; // do we show memory usage etc.?

    public $context = array(); // for passing contextual info (e.g. ancestor nodes)

    public function __construct() {

    }

    public function setUpDefaultVars() {
        // Built-in variables (section 5.1.1 & 5.2.1 of info model)
        $this->response['numAttempts'] = new Variable('single', 'integer', array('value' => 0));
        $this->response['duration'] = new Variable('single', 'float', array('value' => 0));
        $this->outcome['completionStatus'] = new Variable('single', 'identifier', array('value' => 'not_attempted'));

        // TODO: We have this to get around mistakes (?) in the example QTI - should we?
        $this->outcome['completion_status'] = $this->outcome['completionStatus'];
    }

    // TODO: We should be able to pass the form action URL to the controller
    // For example, if we want to remove one of the query string parameters before
    // posting back, or to post to a completely different script.
    public function showItemBody() {
        echo "<form method=\"post\" enctype=\"multipart/form-data\">";
        $resource_provider = $this->resource_provider;
        if(count($this->itemBody) > 0) {
        	print_r($this->itemBody);
        	echo $this->itemBody[0]($this);
        }
        echo "<input type=\"submit\" value=\"Submit response\"/>";
        echo "</form>";
    }

    // TODO: Should this be moved out of the item controller into
    // an engine class?
    public function run() {
        $this->persistence->restore($this);

        if ($this->state == ItemController::STATE_NONE) {
            $this->beginItemSession();
        }

        if ($this->state == ItemController::STATE_INTERACTING) {
            if($this->response_source->isEndAttempt()) {
                // TODO: fix (the person has submitted the item)
                $this->endAttempt();
            }
        }

        // TODO: How do we know when to show the body / results?
        $this->showItemBody();
        $this->displayResults();

        $this->persistence->persist($this);

        $this->beginAttempt();

        if ($this->show_debugging) {
            echo "<hr />Memory: " . memory_get_peak_usage() / (1024 * 1024) . "Mb"; // TODO: Remove this debugging
        }

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

    public function processResponse() {
        $this->response_processing->execute();

        if ($this->modal_feedback_processing) {
            echo $this->modal_feedback_processing->execute();
        }
    }

    public function displayResults() {
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
}