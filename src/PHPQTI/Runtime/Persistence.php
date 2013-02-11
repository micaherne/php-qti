<?php

namespace PHPQTI\Runtime;

interface Persistence {

    public function persist($controller);
    public function restore($controller);
    public function reset($controller);

}