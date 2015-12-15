<?php
	namespace App\Controllers;

	class AdminController {

		public function indexAction($params = null) {
			$quotes = \App\SuggestedQuote::listSuggestedQuotes();
			//print_r($quotes);
			getSystem()->render('admin', array('squotes' => $quotes));
		}

	}