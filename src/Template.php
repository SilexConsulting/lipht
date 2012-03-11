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
		if ($this->hasChildNodes()) {
			foreach ($this->childNodes as $child){
				$child->bind();
			}
		}
		if ($this->hasAttributes()) {
			foreach ($this->attributes as $index => $attr){
				if ($index == 'clearable'){
					$this->parentNode->removeChild($this);
				}
				if ($index == 'lift'){
					$val = $attr->value;
					list($class, $method) = split('::', $val);
					if (!class_exists($class)) {
						Loader::loadSnippet($class);
					}
					$ret = $class::{$method}($this);
					$this->parentNode->replaceChild($ret, $this);
					$ret->parentNode->bind();
				}
			}
		}
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