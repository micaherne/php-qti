<?php

namespace PHPQTI\Runtime\Impl;

use PHPQTI\Runtime\QTIVariable;
use PHPQTI\Runtime\ResponseSource;

// TODO: Support file upload
class HttpResponseSource implements ResponseSource {

    /**
     * Update a variable with values from $_POST
     * @param string $name
     * @param qti_variable $variable
     */

    public function bindVariable($name, QTIVariable &$variable) {
        switch ($variable->cardinality) {
            case 'single':
                if( $submittedvalue = $this->get($name)) {
                    $variable->value = $submittedvalue;
                    if ($variable->type == 'directedPair') {
                        // Gap is target, value is source
                        foreach($submittedvalue as $target => $source) {
                            $variable->value = "$source $target";
                            break; // There should be only one
                        }
                    } else if ($variable->type == 'boolean') {
                        $variable->value = ($submittedvalue == 'true');
                    }
                }
                break;
            case 'multiple':
                if($submittedvalue = $this->get($name)) {
                    if (is_array($submittedvalue)) {
                        $variable->value = $submittedvalue;
                    } else {
                        $variable->value = array($submittedvalue);
                    }
                    if ($variable->type == 'directedPair') {
                        $variable->value = array();
                        // Gap is target, value is source
                        // This is a bit over-complicated to deal with matchInteraction
                        foreach($submittedvalue as $target => $source) {
                            if (!is_array($source)) {
                                $source = array($source);
                            }
                            foreach($source as $s) {
                                $variable->value[] = "$s $target";
                            }
                        }
                    } else if ($variable->type == 'point') {
                        $variable->value = explode(',', $submittedvalue);
                    }
                }
                break;
            case 'ordered':
                /* Ordered variables use inputs with names like:
                 * RESPONSE[choiceA] which have integer values giving
                 * the order
                 *
                 * TODO: Deal with unset options
                 */
                $values = $this->get($name);
                if (!is_null($values)) {
	                $values = array_flip($values);
	                ksort($values);
	                $variable->value = array_values($values);
                }
                break;
            default:
                throw new Exception('qti_http_response_source does not support variable cardinality ' . $variable->cardinality);
        }
         
    }

    public function get($name) {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        } else {
            return null;
        }
    }

    public function isEndAttempt() {
        return count($_POST) > 0; // TODO: Finish - how do we really check if they've ended the attempt
    }

}