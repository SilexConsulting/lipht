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
		$start = 0;
		while ($start = $this->containsPartials($start)){
			$start = $this->extractPartial($start);
		}
		return $this->partials;
	}
	
	private function extractPartial($start){
		//find the start of the lift comment signifying the start of a partial
		$regex = '/<!--[ ]+?Lift::([\:]+?)::Start[ ]+?-->([.]*)<!--[ ]+?Lift::\1::End[ ]+?-->/';
		
		$end = strpos($this->html, '<!-- Lift::', $start+1);
		//find the end of the start lift comment
		$eot = strpos($this->html, '-->', $start);
		//extract the lift comment and the partial name
		$liftCommment = trim(substr($this->html, $start + 4, $eot - $start -4));
		$templateName = $this->getPartialName($liftCommment);
		//Find the end of the closing lift comment
		$end = strpos($this->html, '-->', $end) + 3;
		//extract the partial.
		$partial = substr($this->html, $start, $end - $start);
		
		if (!array_key_exists($templateName,$this->partials)){
			$this->partials[$templateName] = $partial;
		}
		return $end;
	} 
}
