<?php
	
	require __DIR__ . '/vendor/autoload.php';

	$system = new App\System; 
	$router = new AltoRouter();
	$system->setRouter($router);

	$router->map( 'GET', '/', function() {
		echo "This is the Home page finally!";
	}, 'home' );

	$router->map('GET', '/test', '\App\Controllers\HomeController#indexAction', 'test');
	$router->map('GET', '/test/[i:id]', '\App\Controllers\HomeController#indexAction', 'testWithId');

	$system->renderRouterMatch();