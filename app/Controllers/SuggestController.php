<?php
	
	namespace App\Controllers;
	use \App\Render;

	class SuggestController {

		private $suggestedQuote;

		public function indexAction($params = null) {
			$vars['recaptchaSiteKey'] = getenv('RECAPTCHA_SITE_KEY');
			getSystem()->render('suggest', $vars);
		}

		public function thanksAction($params = null) {
			getSystem()->render('suggestThanks');
		}

		public function postSuggestion($params) {
			if(isset($_POST['doSuggest'])) {
				$recaptcha = new \ReCaptcha\ReCaptcha(getenv('RECAPTCHA_SECRET'));
				$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
				 
				if ($resp->isSuccess()) {
				 	
					$err = $this->checkForInputErrors($_POST);
					if($err)
						getSystem()->render('suggest', array_merge($_POST, array('recaptchaSiteKey' => getenv('RECAPTCHA_SITE_KEY'), 'errors' => $err)));
					else
						$this->cleanAndSave($_POST);
				} else {
					getSystem()->getRender()->error(500, 'It appears that you are a Robot. No luck here!');
				}
			}
		}

		private function cleanAndSave($data) {
			$this->suggestedQuote = new \App\SuggestedQuote($data);
			try {
				$this->suggestedQuote->save();
			} catch(\Exception $e) {
				getSystem()->getRender()->error(500, "Could not create suggestion", $e);
			}
		}

		// @TODO: Think clean code. Separate these into multiple smaller functions. One for each validation.
		private function checkForInputErrors($data) {
			$errors = [];
			
			// Validate quote
			if(strlen($data['quote']) < 20 || 
				strlen($data['quote']) > 300)
					$erros[] = "Citatet måste vara mer än 20 tecken, men inte längre än 300 tecken.";

			// Validate person name
			if(strlen($data['person']) < 5 || 
				strlen($data['person']) > 35 || 
				$this->containsNumbers($data['person']) ||
				$this->validateString($data['person']))
					$errors[] = "Kontrollera namnet. Måste vara mellan 5 och 35 tecken och får inte innehålla siffror (för vilket namn gör det!?). Inga konstiga specialtecken.";

			// Validate position
			if(strlen($data['position']) < 5 || 
				strlen($data['position']) > 45)
					$errors[] = "Kontrollera arbetstiteln. Måste vara mellan 5 och 45 tecken.";

			// Validate date
			if(strlen($data['quoteDate']) < 5 || 
				strlen($data['quoteDate']) > 25 ||
				$this->validateString($data['quoteDate']))
					$errors[] = "Kontrollera datumet. Måste vara mellan 5 och 25 tecken. Inga konstiga specialtecken.";

			// Validate date
			if(strlen($data['source']) < 5 || 
				strlen($data['source']) > 255)
					$errors[] = "Kontrollera källan. Måste vara mellan 5 och 254 tecken.";


			if(sizeof($errors) < 1)
				return false;
			else
				return $this->errorsToHtml($errors);
		}

		private function errorsToHtml($errors) {
			$re = "";
			foreach ($errors as $key => $value)
				$re .= "<li>" . $value . "</li>";

			return $re;
		}
	
		private function containsNumbers($String){
			return preg_match('/\\d/', $String) > 0;
		}

		private function validateString($string) {
			return preg_match('/[\'^£$%&*()}"\'{@#~?><>,|=_+¬]/', $string);
		}

		private function testDb() {
			$db = getSystem()->getDb();
			$db->query("SELECT * FROM quotes");
		}
	}