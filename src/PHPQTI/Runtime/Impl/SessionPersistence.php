<?php

namespace PHPQTI\Runtime\Impl;

use PHPQTI\Runtime\Persistence;

class SessionPersistence implements Persistence {

    public function persist($controller) {
        if(!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION[$controller->identifier])) {
            $_SESSION[$controller->identifier] = array();
        }
        $_SESSION[$controller->identifier]['response'] = $controller->response;
        $_SESSION[$controller->identifier]['outcome'] = $controller->outcome;
        $_SESSION[$controller->identifier]['template'] = $controller->template;
        $_SESSION[$controller->identifier]['state'] = $controller->state;
    }

    public function restore($controller) {
        session_start();
        if (!isset($_SESSION[$controller->identifier])) {
            return;
        }
        $sessionvariable = $_SESSION[$controller->identifier];
        if (!isset($sessionvariable['state'])) {
            return;
        }
        $controller->response = $sessionvariable['response'];
        $controller->outcome = $sessionvariable['outcome'];
        $controller->template = $sessionvariable['template'];
        $controller->state = $sessionvariable['state'];
    }

    public function reset($controller) {
        session_start();
        unset($_SESSION[$controller->identifier]);
    }

}