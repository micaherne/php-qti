<?php
 
namespace PHPQTI\Model;
 
use PHPQTI\Model\Base\Interaction;
use PHPQTI\Model\Base\Inline;
use PHPQTI\Model\Base\Flow;
use PHPQTI\Model\Base\InlineInteraction;

class EndAttemptInteraction extends \PHPQTI\Model\Gen\EndAttemptInteraction 
    implements InlineInteraction, Flow, Inline, Interaction {

    protected $_elementName = 'endAttemptInteraction';

    public function __invoke($controller) {
        $variableName = $this->responseIdentifier;
        $result = "<div id=\"endAttemptInteraction_{$variableName}\" class=\"qti_endAttemptInteraction\" method=\"post\">";
        $result .= "<input type=\"hidden\" name=\"{$variableName}\" value=\"false\" />";
        $result .= "<input type=\"submit\" value=\"{$this->title}\" >";
        $result .= "</div>";
        return $result;
    }
}