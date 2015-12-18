<?php
	
	namespace App\Controllers;
	use \App\Render;

	class HomeController {

		public function indexAction($params = null) {
			$randomQuote = \App\Quote::getRandomQuoteId();
			$quote = new \App\Quote;
			$quote->load($randomQuote);
			getSystem()->render('home', $quote->toArray());
		}

		public function quoteAction($params) {
			try {
				$quote = new \App\Quote;
				$quote->load($params['qId']);
				getSystem()->render('home', $quote->toArray());		
			} catch(\Exception $e) {
				getSystem()->getRender()->error(500, "Error while loading quote", $e);
			}
		}

		private function testDb() {
			$db = getSystem()->getDb();
			$db->query("SELECT * FROM quotes");
		}
	}