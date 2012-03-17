<?php
namespace Lift;

include "Loader.php";

use DOMDocument;
use DOMNode;
use DOMDocumentType;
use DOMElement;
use DOMText, DOMComment, DOMProcessingInstruction;
use Exception;

trait Lifty {
	private $partials = [];
	function addToPartial($templateName, $node){
		$this->partials[$templateName] = $node;
	}
	
	function isPartialTemplateStart(){
		if ($pData = $this->getPartialTemplateData()){
			list($class, $template, $pos) = $pData;
			if (trim($class) == 'lift' && trim($pos) == 'start'){
				return $template;
			}
		}
	}

	function isPartialTemplateEnd($templateName){
		if ($pData = $this->getPartialTemplateData()){
			list($class, $template, $pos) = $pData;
			return (trim($class) == 'lift' && trim($pos) == 'end' && $template == $templateName);
		}
	}

	function getPartialTemplateData(){
		if (!strstr($this->nodeValue, '::')) return false;
		return explode('::', $this->nodeValue);
	}
	
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
		//save partial templates unprocessed for use elsewhere before processing them
		if ($this instanceof LiftComment && $templateName = $this->isPartialTemplateStart()) {
			//from this point in we need to put all nodes and their children into a partial template
			
			$this->addToPartial($template, $this);
			$node = $this->nextSibling;
			while ($node && ($node instanceof LiftComment) && $node->isPartialTemplateEnd($templateName)){
				echo $node->nodeName . "::" . $node->nodeValue . "<br/>";
				$this->addToPartial($template, $node);
				$node = $node->nextSibling;
			}
			
			$this->addToPartial($template, $this);
		}
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