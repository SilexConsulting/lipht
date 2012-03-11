<?php
class Menu{
	function Top($node){
		$node->appendChild($node->ownerDocument->createElement('child', 'somevalue'));

		$newNode = $node->ownerDocument->createElement('child', 'somevalue');
		return $node;
	}
	function Item($node){
		return $node->ownerDocument->createElement('li', 'somevalue');
	}
}