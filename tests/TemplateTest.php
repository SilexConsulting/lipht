<?php
namespace Lift\Tests;

use Lift\LiftTemplate;


class TempalteTest extends \PHPUnit_Framework_TestCase
{
	public function testLoadHtmlFile(){
		include 'snippets/Menu.php';
		$template = new LiftTemplate(__DIR__ . '/data/homepage.html');
		$template->getHTML();
	}
}