<?php

namespace PHPQTI\Runtime\Element;

class AreaMapEntry extends Element {

	public function __invoke($controller) {
		return $this->attrs;
	}

}