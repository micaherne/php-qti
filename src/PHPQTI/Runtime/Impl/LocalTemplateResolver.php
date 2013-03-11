<?php

namespace PHPQTI\Runtime\Impl;

use PHPQTI\Util\XMLUtils;

use PHPQTI\Runtime\Exception\ProcessingException;

use PHPQTI\Runtime\TemplateResolver;

/**
 * Template resolver which looks for the templates in a specific directory.
 * 
 * @author Michael Aherne
 *
 */
class LocalTemplateResolver implements TemplateResolver {
	
	protected $templateDirectory;
	
	public function __construct($templateDirectory) {
		$this->templateDirectory = $templateDirectory;
	}
	
	public function getTemplate($template, $templateLocation = null) {
		if (! strpos($template, 'http://www.imsglobal.org/question/qti_v2p1/rptemplates/') === 0) {
			throw new ProcessingException("Template location must start with 'http://www.imsglobal.org/question/qti_v2p1/rptemplates/'");
		}
		
		$template = str_replace("http://www.imsglobal.org/question/qti_v2p1/rptemplates/", '', $template);
		
		if (strpos($template, '/') !== false) {
			throw new ProcessingException("Template contains illegal characters");
		}
		
		$templatePath = $this->templateDirectory . '/' . $template . '.xml';
		if (!file_exists($templatePath)) {
			throw new ProcessingException("Template file not found: $templatePath");
		}
		
		$dom = new \DOMDocument();
		$dom->load($templatePath);
		$xmlutils = new XMLUtils();
		return $xmlutils->unmarshall($dom);
	}
	
}