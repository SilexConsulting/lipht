<?php

namespace Lift;

class LiftHTMLFile {
	public $filename = '';
	private $html;
	private $partials = [];
	public function __construct($filename){
		$this->filename = $filename;
	}

	private function loadHtml(){
		if (!$this->html) {
			$this->html = file_get_contents($this->filename);
		}
	}
	
	private function containsPartials($start){
		return strpos($this->html, '<!-- Lift::', $start);
	}

	private function getPartialName($tag){
		$pData = explode('::', $tag);
		assert(count($pData) == 3);
		list($class, $name, $pos) = $pData;
		return $name;
	}
	public function getHtml(){
		$this->loadHtml();
		return $this->html;
	}

	public function getPartials(){
		$this->loadHtml();
		$this->html = $this->extractPartial($this->html);
		return $this->partials;
	}

	
	private function extractPartial($html){
		$partialFinder = '/<!-- +Lift::([^:]+?)::Start +-->\n(.*)\n<!-- +Lift::\1::End +-->/s';
		if (preg_match($partialFinder, $html, $matches)) {
			
			$templateName = $matches[1];
			$partial = $matches[2];
			$partial = $this->extractPartial($partial);
			$replacement = "<lift include::$templateName />";
			$html = preg_replace($partialFinder, $replacement, $html, 1);
			if (!array_key_exists($templateName,$this->partials)){
				$this->partials[$templateName] = $partial;
			}
				
		} 
		if (preg_match($partialFinder, $html, $matches)){
			$html = $this->extractPartial($html);
		}
		return $html;
	} 
}
