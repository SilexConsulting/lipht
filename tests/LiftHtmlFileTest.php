<?php
namespace Lift\Tests;

use Lift\LiftHtmlFile;
use Lift\LiftTemplateRegistry;


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
<!-- global head template -->
<lift:include template="Title" />
</head>
EOD;
		$foot = <<< 'EOD'
    <div>
        <ul><li>Footer1</li><li>Footer2</li><li>Footer3</li></ul>
    </div>
EOD;
		$filename = __DIR__ . '/data/SimplePartials.html';
		$x = new LiftHTMLFile($filename);
		$templateRegistry = LiftTemplateRegistry::getInstance();
		$templateRegistry->scanForPartials();
		$html = $x->getHtml();
		
		
		$partials =  $templateRegistry->partials;
		$this->assertEquals(5, count($partials));
		$this->assertEquals($head, $partials['Head']);
		$this->assertEquals($foot, $partials['Footer']);
	}

}