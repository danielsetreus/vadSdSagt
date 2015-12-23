<?php
session_start();	
	require __DIR__ . '/vendor/autoload.php';

	$system = new App\System; 
	$router = new AltoRouter();
	$system->setRouter($router);

	require __DIR__ . '/app/helpers.php';

	$router->map( 'GET', '/', '\App\Controllers\HomeController#indexAction', 'home');
	$router->map( 'GET', '/[i:qId]', '\App\Controllers\HomeController#quoteAction', 'oneQuote');
	$router->map( 'GET', '/[i:qId]/prev', '\App\Controllers\HomeController#quotePrevAction', 'oneQuotePrev');
	$router->map( 'GET', '/[i:qId]/next', '\App\Controllers\HomeController#quoteNextAction', 'oneQuoteNext');

	$router->map('GET', '/about', '\App\Controllers\AboutController#indexAction', 'about');

	$router->map('GET', '/tipsa', '\App\Controllers\SuggestController#indexAction', 'suggest');
	$router->map('GET', '/tipsa/tack', '\App\Controllers\SuggestController#thanksAction', 'suggestThanks');
	$router->map('POST', '/tipsa', '\App\Controllers\SuggestController#postSuggestion', 'suggestPost');

	$router->map('GET', '/login', '\App\Controllers\LoginController#indexAction', 'loginForm');
	$router->map('POST', '/login', '\App\Controllers\LoginController#loginAction', 'loginPost');

	$router->map( 'GET', '/admin', '\App\Controllers\AdminController#indexAction', 'admin');
	$router->map( 'GET', '/admin/approve/[i:sqId]', '\App\Controllers\AdminController#approveAction', 'adminApprove');
	$router->map( 'POST', '/admin/approve/[i:sqId]', '\App\Controllers\AdminController#approvePostAction', 'adminApprovePost');
	$router->map( 'POST', '/admin/delete/sq/[i:sqId]', '\App\Controllers\AdminController#deleteSuggestionPostAction', 'deleteSuggestionPost');

	$router->map( 'GET', '/admin/quotes', '\App\Controllers\AdminController#quotesAction', 'adminQuotes');
	$router->map( 'GET', '/admin/quotes/create', '\App\Controllers\AdminController#quotesCreateAction', 'adminCreateQuote');
	$router->map( 'POST', '/admin/quotes/create', '\App\Controllers\AdminController#quotesCreateActionPost', 'adminPostCreateQuote');
	$router->map( 'GET', '/admin/quotes/[i:qId]', '\App\Controllers\AdminController#quotesEditAction', 'adminEditQuote');
	$router->map( 'POST', '/admin/quotes/[i:qId]/edit', '\App\Controllers\AdminController#quotesEditPostAction', 'adminEditQuotePost');
	$router->map( 'POST', '/admin/quotes/[i:qId]/delete', '\App\Controllers\AdminController#quotesDeletePostAction', 'adminDeleteQuotePost');

	$router->map( 'GET', '/admin/persons/[*:name]', '\App\Controllers\AdminController#searchPersonAction', 'adminSearchPerson');
	$router->map( 'POST', '/admin/persons/imageUpload', '\App\Controllers\AdminController#imageUploadAction', 'imageUpload');

	$router->map('GET', '/info', function() {
		phpinfo();
	});

	$system->renderRouterMatch();