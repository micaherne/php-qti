<?php

namespace PHPQTI\Runtime;

use PHPQTI\Runtime\Processing\Variable;

interface ResponseSource {

    public function bindVariable($name, Variable &$variable);
    public function get($name);
    public function isEndAttempt();

}