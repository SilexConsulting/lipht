<?php

namespace Lift;

//include "Loader.php";

class LiftTemplateRegistry {
	private $_partials = [];
	private $_templates = [];
	public $basedir;
		
	public static function getInstance(){
		static $instance;
		if (!$instance){
			$instance = new LiftTemplateRegistry();
		}
		return $instance;
	}

	public function getTemplate($filename){
		$namespace = $this->getNamespace($filename);
		if (!array_key_exists($namespace, $this->_templates)){
			$this->_templates[$namespace] = $this->extractPartial(file_get_contents($filename));
		}
		return $this->_templates[$namespace];
	}

	public function getPartial($namespace){
		if (!array_key_exists($namespace, $this->_partials)){
			throw new \Exception("I don't know about the partial template: $namespace");
		}
		return $this->_partials[$namespace];
	}

	public function scanForPartials(){
		$templates = [];
		$filespec = '/*';
		$basedir = $this->basedir.$filespec;
		$path[] = $basedir;
		while(count($path) != 0)
		{
			$search = array_shift($path);
			foreach(glob($search) as $item)
			{
				if (is_dir($item)){
					$path[] = $item . $filespec;
				}elseif (is_file($item))
				{
					if (fnmatch("_*.html", basename($item))){
						$templates[] = $item;
	
					}
				}
			}
		}
		foreach ($templates as $template){
			$namespace = $this->getNamespace($template);
			if(! array_key_exists($namespace,$this->_partials)){
				$this->_partials[$namespace] = $this->extractPartial(file_get_contents($template));
			}
				
		}
	}	
	
	private function getNamespace($filename){
		$subpath = substr(dirname($filename), strlen($this->basedir)+1);
		$parts = [];
		if ($subpath) {
			$parts = explode("/", $subpath);
		}
		$parts[] = substr(basename($filename, '.html'),1);
		$namespace = implode("::", $parts);
		return $namespace;
	}

	private function extractPartial($html){
		$partialFinder = '/<!-- +Lift::([^:]+?)::Start +-->\n(.*)\n<!-- +Lift::\1::End +-->/s';
		if (preg_match($partialFinder, $html, $matches)) {
				
			$templateName = $matches[1];
			$partial = $matches[2];
			$partial = $this->extractPartial($partial);
			$replacement = "<lift:include template=\"$templateName\" />";
			$html = preg_replace($partialFinder, $replacement, $html, 1);
			if (!array_key_exists($templateName,$this->_partials)){
				$this->_partials[$templateName] = $partial;
			}
	
		}
		if (preg_match($partialFinder, $html, $matches)){
			$html = $this->extractPartial($html);
		}
		return $html;
	}
	
	public function __get($name){
		if ($name == "partials"){
			return $this->getPartials();
		}
	}
	
	private function getPartials(){
		$this->scanForPartials();
		return $this->_partials;
	}

}