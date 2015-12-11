<?php
	
	require __DIR__ . '/vendor/autoload.php';

	$system = new App\System; 
	$router = new AltoRouter();
	$system->setRouter($router);

	require __DIR__ . '/app/helpers.php';

	$router->map( 'GET', '/', '\App\Controllers\HomeController#indexAction', 'home');
	$router->map('GET', '/test', '\App\Controllers\HomeController#indexAction', 'test');
	$router->map('GET', '/test/[i:id]', '\App\Controllers\HomeController#indexAction', 'testWithId');

	$router->map('GET', '/info', function() {
		phpinfo();
	});

	$system->renderRouterMatch();