<?php

namespace Lift;

class LiftHTMLFile {
	public $filename = '';
	private $html;
	private $_templateRegistry;
	public function __construct($filename){
		$this->filename = $filename;
		$this->_templateRegistry = LiftTemplateRegistry::getInstance();
		$this->_templateRegistry->basedir = dirname($filename);
	}

	public function getHtml(){
		$this->loadHtml();
		return $this->replacePartial($this->html);
	}

	private function loadHtml(){
		if (!$this->html) {
			$this->html = $this->_templateRegistry->getTemplate($this->filename);
		}
	}

	private function replacePartial($html){
		$includeFinder = "/<lift:include template=\"([^\"]+?)\" \/>/s";
		if (preg_match($includeFinder, $html, $matches)){
			$templateName = $matches[1];
			$partial = $this->_templateRegistry->getPartial($templateName);
			$html = preg_replace("/<lift:include template=\"$templateName\" \/>/s", $partial, $html);
			if (preg_match($includeFinder, $html)){
				$html = $this->replacePartial($html);
			}
		}
		return $html;
	}



}
