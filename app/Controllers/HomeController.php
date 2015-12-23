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
			$this->loadQuote($params['qId'], null);
		}

		public function quotePrevAction($params) {
			$this->loadQuote($params['qId'], 'prev');
		}

		public function quoteNextAction($params) {
			$this->loadQuote($params['qId'], 'next');
		}

		private function loadQuote($id, $target) {
			try {
				$quote = new \App\Quote;
				
				if($target == 'prev')
					$quote->loadPrev($id);
				elseif($target == 'next')
					$quote->loadNext($id);
				else
					$quote->load($id);

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