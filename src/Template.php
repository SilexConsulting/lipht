<?php
namespace Lift;

include "Loader.php";

use DOMDocument;
use DOMNode;
use DOMDocumentType;
use DOMElement;
use DOMText, DOMComment, DOMProcessingInstruction;


trait Lifty {
	
	function bind(){
		$arr = [];
		if ($this->hasChildNodes()) {
			foreach ($this->childNodes as $child){
				$arr = array_merge($arr,  $child->bind());
			}
			foreach ($arr as $node){
				$this->removeChild($node);
			}
			$arr = [];
		}
		if ($this->hasAttributes()) {
			foreach ($this->attributes as $index => $attr){
				if ($index == 'clearable'){
					//$this->parentNode->removeChild($this);
					$arr[] = $this;
				}
				if ($index == 'lift'){
					$val = $attr->value;
					list($class, $method) = explode('::', $val);
					if (!class_exists($class)) {
						Loader::loadSnippet($class);
					}
					$ret = $class::{$method}($this);
					if ($this->parentNode){
						//$this->parentNode->replaceChild($ret, $this);
						$this->parentNode->insertBefore($ret, $this);
						//$this->parentNode->removeChild($this);
						if  (!in_array($this, $arr)) {
							$arr[] = $this;
						}
					} else {
						echo "wtf went wrong?";
						print_r($ret->nodeName);
						print_r($ret->nodeValue);
					}
				}
			}
		}
		return $arr;
	}
}

class LiftNode extends DOMNode{
	use Lifty;
}
class LiftDocumentType extends DOMDocumentType{
	use Lifty;
}
class LiftElement extends DOMElement{
	use Lifty;
}
class LiftText extends DOMText{
	use Lifty;
}
class LiftComment extends DOMComment {
	use Lifty;
}
class LiftProcessingInstruction extends DOMProcessingInstruction {
	use Lifty;
}
class LiftDocument extends DOMDocument{
	use Lifty;
} 

class LiftTemplate extends DOMDocument{
	private $html;
	public function __construct($document){
		$doc = new LiftDocument();
		$doc->registerNodeClass('DOMDocument', 'Lift\LiftDocument');
		$doc->registerNodeClass('DOMNode', 'Lift\LiftNode');
		$doc->registerNodeClass('DOMElement', 'Lift\LiftElement');
		$doc->registerNodeClass('DOMText', 'Lift\LiftText');
		$doc->registerNodeClass('DOMComment', 'Lift\LiftComment');
		$doc->registerNodeClass('DOMDocumentType', 'Lift\LiftDocumentType');
		$doc->registerNodeClass('DOMProcessingInstruction', 'Lift\LiftProcessingInstruction');
		$doc->LoadHTMLFile($document);
		$doc->bind();
		$this->html = $doc->saveHTML();
	}
	
	public function getHTML(){
		return $this->html;
	}
}