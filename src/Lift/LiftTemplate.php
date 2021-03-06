<?php
namespace Lift;

include "Loader.php";

use DOMDocument;
use DOMNode;
use DOMDocumentType;
use DOMElement;
use DOMText, DOMComment, DOMProcessingInstruction;
use Exception;

trait LiftPartial {

}

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
		$template = '';
		if ($this->hasAttributes()) {
			foreach ($this->attributes as $index => $attr){
				if ($index == 'clearable'){
					$arr[] = $this;
				}
				if ($index == 'lift'){
					$snippet = $attr->value;
					$newNode = null;
					list($class, $method) = explode('::', $snippet);
					try {
						if (!class_exists($class)) {
							Loader::loadSnippet($class);
						}
						$newNode = $class::{$method}($this);
					} catch (Exception $e) {
						$newNode = $this->ownerDocument->createElement('text', $e->getMessage());
					}
			
					if ($newNode && $this->parentNode){
						$this->parentNode->insertBefore($newNode, $this);
						if  (!in_array($this, $arr)) {
							$arr[] = $this;
						}
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

class LiftTemplate {
	private $html;
	private $doc;
	private function loadHtml($html){
		if ($html instanceof LiftHTMLFile){
			$this->doc->loadHTML($html->getHTML());
		} else if ($html instanceof String){
			if (file_exists($html)){
				$this->doc->loadHTMLFile($html);
			} else {
				$this->doc->loadHTML($html);
			}
			
		}
		return $this;
	}

	public function __construct(){
		$this->doc = new LiftDocument();
		$this->doc->registerNodeClass('DOMDocument', 'Lift\LiftDocument');
		$this->doc->registerNodeClass('DOMNode', 'Lift\LiftNode');
		$this->doc->registerNodeClass('DOMElement', 'Lift\LiftElement');
		$this->doc->registerNodeClass('DOMText', 'Lift\LiftText');
		$this->doc->registerNodeClass('DOMComment', 'Lift\LiftComment');
		$this->doc->registerNodeClass('DOMDocumentType', 'Lift\LiftDocumentType');
		$this->doc->registerNodeClass('DOMProcessingInstruction', 'Lift\LiftProcessingInstruction');
	}

	public function bind($html){
		$this->loadHtml($html);
		$this->doc->bind();
		$this->html = $this->doc->saveHTML();
		return $this;
	}

	public function getHtml(){
		return $this->html;
	}

}