<?php 

class Menu{
	static function Top($node){
		$newNode = $node->ownerDocument->createElement('child', 'top Item');
		return $newNode;
	}
	static function Item($node){
		return $node->ownerDocument->createElement('li', 'Item');
	}
}