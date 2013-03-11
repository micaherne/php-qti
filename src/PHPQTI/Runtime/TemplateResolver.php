<?php

namespace PHPQTI\Runtime;

interface TemplateResolver {
	
	public function getTemplate($template, $templateLocation = null);
	
}