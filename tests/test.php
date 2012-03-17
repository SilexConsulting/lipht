<?php
//require_once __DIR__.'/silex.phar';
include __DIR__ .'/src/Template.php';
use Lift\LiftTemplate;
$template = new LiftTemplate('homepage.html');
echo $template->getHTML();

/*$app = new Silex\Application();

$app->get('/hello/{name}', function ($name) use ($app) {
	$template = new LiftTemplate($name);
	echo $template->getHTML();
});

$app->run();*/