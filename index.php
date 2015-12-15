<?php
session_start();	
	require __DIR__ . '/vendor/autoload.php';

	$system = new App\System; 
	$router = new AltoRouter();
	$system->setRouter($router);

	require __DIR__ . '/app/helpers.php';

	$router->map( 'GET', '/', '\App\Controllers\HomeController#indexAction', 'home');
	$router->map('GET', '/about', '\App\Controllers\AboutController#indexAction', 'about');

	$router->map('GET', '/tipsa', '\App\Controllers\SuggestController#indexAction', 'suggest');
	$router->map('GET', '/tipsa/tack', '\App\Controllers\SuggestController#thanksAction', 'suggestThanks');
	$router->map('POST', '/tipsa', '\App\Controllers\SuggestController#postSuggestion', 'suggestPost');

	$router->map('GET', '/login', '\App\Controllers\LoginController#indexAction', 'loginForm');
	$router->map('POST', '/login', '\App\Controllers\LoginController#loginAction', 'loginPost');

	$router->map( 'GET', '/admin', '\App\Controllers\AdminController#indexAction', 'admin');

	$router->map('GET', '/info', function() {
		phpinfo();
	});

	$system->renderRouterMatch();