<?php

namespace PHPQTI\Runtime\Element;

class MapEntry extends Element {

	public function __invoke($controller) {
		return $this->attrs;
	}

}