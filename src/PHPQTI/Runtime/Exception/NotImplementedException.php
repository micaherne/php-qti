<?php

//    PHP-QTI - a PHP library for QTI v2.1
//    Copyright (C) 2013 Michael Aherne
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program. If not, see <http://www.gnu.org/licenses/>.

namespace PHPQTI\Runtime\Exception;

class NotImplementedException extends \Exception {
    
    public $elementName = null;
    
    public function __construct($elementName, $message = null, $code = 0, Exception $previous = null) {
        
        parent::__construct($message, $code, $previous);
        $this->elementName = $elementName;
        
    }
    
}