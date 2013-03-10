<?php

namespace PHPQTI\Runtime;

use PHPQTI\Runtime\QTIVariable;

interface ResponseSource {

    public function bindVariable($name, QTIVariable &$variable);
    public function get($name);
    public function isEndAttempt();

}