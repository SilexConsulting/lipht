<?php
namespace Lift;

class Loader {
	function __construct(){
		//Load all the things?
		//singleton?
	}
	
	static function loadSnippet($className){
		if (!class_exists($className)){
			require __DIR__ . "/snippets/$className.php";
		}
	} 
}