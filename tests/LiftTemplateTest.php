<?php
namespace Lift\Tests;

use Lift\LiftTemplate;


class TempalteTest extends \PHPUnit_Framework_TestCase
{
	public function testLoadHtmlFile(){
		return true;
		//include 'snippets/Menu.php';
		$mock = $this->getMock('Menu');
 		$mock->expects($this->exactly(1))
			->method('Top');
 		$mock->expects($this->exactly(1))
 			->method('Item');	
		$template = new LiftTemplate();
		$template->bind(__DIR__ . '/data/homepage.html');
	}
}