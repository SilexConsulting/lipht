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
<!-- Lift::Head::Start -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
</head>
<!-- Lift::Head::End -->
EOD;
		$foot = <<< 'EOD'
<!-- Lift::Footer::Start -->
    <div>
        <ul><li>Footer1</li><li>Footer2</li><li>Footer3</li></ul>
    </div>
<!-- Lift::Footer::End -->
EOD;
		$filename = __DIR__ . '/data/SimplePartials.html';
		$html = new LiftHTMLFile($filename);
		$partials = $html->getPartials();
		$this->assertEquals(2, count($partials));
		$this->assertEquals($head, $partials['Head']);
		$this->assertEquals($foot, $partials['Footer']);
	}
}