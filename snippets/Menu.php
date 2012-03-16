<?php 
class Menu{
	function Top($node){
		$newNode = $node->ownerDocument->createElement('child', 'top Item');
		return $newNode;
	}
	function Item($node){
		return $node->ownerDocument->createElement('li', 'Item');
	}
}