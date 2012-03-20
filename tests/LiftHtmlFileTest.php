<?php
namespace Lift\Tests;

use Lift\LiftHtmlFile;


class LiftHtmlFileTest extends \PHPUnit_Framework_TestCase
{
	private $fileContents;
	public function testLoadHtmlFile(){
		$filename =  __DIR__ . '/data/simple.html';
		$fileContents = file_get_contents($filename);
		$html = new LiftHTMLFile($filename);
		$this->assertEquals($fileContents, $html->getHtml());
	}
	
	public function testThatPartialsGetDetected(){
		$head = <<< 'EOD'
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<lift include::Title />
</head>
EOD;
		$foot = <<< 'EOD'
    <div>
        <ul><li>Footer1</li><li>Footer2</li><li>Footer3</li></ul>
    </div>
EOD;
		$filename = __DIR__ . '/data/SimplePartials.html';
		$html = new LiftHTMLFile($filename);
		$partials = $html->getPartials();
		$this->assertEquals(3, count($partials));
		$this->assertEquals($head, $partials['Head']);
		$this->assertEquals($foot, $partials['Footer']);
	}
}