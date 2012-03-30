<?php
namespace Lift\Tests;

use Lift\LiftTemplate;
use Lift\LiftHTMLFile;


include "tests/snippets/Menu.php";

class TemplateTest extends \PHPUnit_Framework_TestCase
{
	public function testThatSnippetsGetCalled(){
		$this->getMockClass('Menu', ['Top', 'Item'], [], 'MenuMock');
		\MenuMock::staticExpects($this->exactly(1))
			->method('Top');
		\MenuMock::staticExpects($this->exactly(1))
			->method('Item');	
		$template = new LiftTemplate();
		$template->bind(new LiftHtmlFile(__DIR__ . '/data/mocktest.html'));
		
	}

	public function testThatLiftIncludesWork(){
		$template = new LiftTemplate();
		$template->bind(new LiftHtmlFile(__DIR__ . '/data/SimplePartials.html'));
	}
}