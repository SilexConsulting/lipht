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
		if (true){
			$start = 0;
			$end = 0;
			while ($start = $this->containsPartials($end)){
				$end = strpos($this->html, '<!-- Lift::', $start+1);
				$eot = strpos($this->html, '-->', $start);
				$tag = trim(substr($this->html, $start + 4, $eot - $start -4));
				$templateName = $this->getPartialName($tag);
				$end = strpos($this->html, '-->', $end) + 3;
				$partial = substr($this->html, $start, $end - $start);
				if (!array_key_exists($templateName,$this->partials)){
					$this->partials[$templateName] = $partial;
				}
				$start = $end;
			}
		}
		return $this->partials;
	}
}
