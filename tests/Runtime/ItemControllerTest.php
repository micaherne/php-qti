<?php

use PHPQTI\Runtime\ItemController;
use PHPQTI\Runtime\Processing\Variable;
use PHPQTI\Runtime\FunctionGenerator;

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

/**
 * Test ItemController.
 * 
 * @author Michael Aherne
 *
 */
class ItemControllerTest extends PHPUnit_Framework_TestCase {
	
	public function testValueOrVariable() {
	    $c = new ItemController();
	    $c->template['HELLO'] = new Variable('single', 'integer', array('value' => 56));
	    $this->assertEquals('5', $c->valueOrVariable('5'));
	    $this->assertEquals(5, $c->valueOrVariable(5));
	    $this->assertEquals(56, $c->valueOrVariable('{HELLO}'));   
	}
	
}