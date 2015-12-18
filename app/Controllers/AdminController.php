<?php
	namespace App\Controllers;
	use \App\Quote;

	class AdminController {

		public function indexAction($params = null) {
			$quotes = \App\SuggestedQuote::listSuggestedQuotes();
			//print_r($quotes);
			getSystem()->render('admin', array('squotes' => $quotes));
		}

		public function approveAction($params) {
			$sq = new \App\SuggestedQuote();
			try {
				if($sq->load($params['sqId']))
					getSystem()->render('adminApprove', $sq->toArray());
				else
					getSystem()->getRender()->error(500, "No such suggested quote to load");
			} catch(\Exception $e) {
				getSystem()->getRender()->error(500, "Error while loading suggested quote", $e);
			}
		}

		public function approvePostAction($params) {
			$person = $this->getPerson();

			$quote = $this->createQuote(array(
				'quote' => $_POST['quote'],
				'source' => $_POST['source'],
				'quoteDate' => $_POST['quoteDate'],
				'dateAdded' => date("Y-m-d H:i:s"),
				'comment' => $_POST['comment'],
				'person' => $person
			));
			$this->deleteSuggestion($params['sqId']);
			getSystem()->flash('Förslaget har godkänts och är nu publicerat', 'success')->redirect('admin');
		}

		public function deleteSuggestionPostAction($params) {
			if(isset($_POST['doDelete']))
				$this->deleteSuggestion($params['sqId']);
			else
				getSystem()->getRender()->error(500, 'Required parameter is missing');
		}

		private function getPerson() {
			$personData = $this->getPersonFromName($_POST['person']);
			if(count($personData) < 1) {
				// Create new Person
				$person = $this->createPerson(array(
					'name' => $_POST['person'],
					'position' => $_POST['position'],
					'image' => $_POST['imageName']
				));
			} else {
				$person = $this->loadPerson($personData[0]['id']);
				$person->setParams(array(
					'name' => $_POST['person'],
					'position' => $_POST['position'],
					'image' => $_POST['imageName']
				));
				try {
					$person->save();
				} catch(\Exception $e) {
					getSystem()->getRender()->error(500, "Error while saving existing person", $e);
				}
			}
			return $person;
		}

		private function createQuote($params) {
			try {
				$quote = new Quote();
				$quote->setParams($params);
				$quote->save();
				return $quote;
			} catch(\Exception $e) {
				getSystem()->getRender()->error(500, "Error while creating quote", $e);
			}
		}

		private function loadPerson($id) {
			try {
				$p = new \App\Person();
				return $p->load($id);
			} catch(\Exception $e) {
				getSystem()->getRender()->error(500, "Error while loading person", $e);
			}
		}	
		private function createPerson($params) {
			try {
				$person = new \App\Person();
				$person->setParams($params);
				$person->save();
				return $person;
			} catch(\Exception $e) {
				getSystem()->getRender()->error(500, "Error while creating new person", $e);
			}
		}

		private function getPersonFromName($name) {
			try {
				return \App\Person::search($name);
			} catch(\Exception $e) {
				getSystem()->getRender()->error(500, "Error while loading person from name", $e);
			}
		}

		private function deleteSuggestion($id) {
			try {
				$sq = new \App\SuggestedQuote();
				if($sq->load($id))
					$sq->softDelete();
				else
					getSystem()->getRender()->error(500, "Suggested Quote not found");
			} catch(\Exception $e) {
				getSystem()->getRender()->error(500, "Could not delete suggested quote", $e);
			}
		}

		public function searchPersonAction($params) {
			try {
				$result = \App\Person::search(urldecode($params['name']) . "%");
				echo json_encode($result);
			} catch(\Exception $e) {
				getSystem()->getRender()->error(500, 'Could not perform search', $e);
			}
		}

		public function imageUploadAction($params) {
			//var_dump($_FILES);
			$targets = \App\Person::getImageDir();
			$target = $targets['absolute'] . $_FILES['img']['name'];
			$relTarget = $targets['relative'] . $_FILES['img']['name'];
			move_uploaded_file($_FILES['img']['tmp_name'], $target);
			
			if(!$_FILES['img']['name']) {
				echo json_encode(array('code' => 1));
			}
			elseif(file_exists($target))
				echo json_encode(array('code' => 0, 'name' => $_FILES['img']['name'], 'url' => $relTarget));
			else
				echo json_encode(array('code' => 1));
		}

		public  function quotesAction($params = null) {
			$quotes = \App\Quote::listQuotes();
			getSystem()->render('adminQuotes', array('quotes' => $quotes));
		}

		public function quotesEditAction($params) {
			$quote = new \App\Quote;
			$quote->load($params['qId']);
			getSystem()->render('adminQuoteEdit', $quote->toArray());
		}

		public function quotesCreateAction($params = null) {
			getSystem()->render('adminQuoteCreate');
		}

		public function quotesEditPostAction($params) {
			$person = $this->getPerson();
			$quote = new \App\Quote;
			try {
				$quote->load($params['qId']);
				$this->setQuoteParams($quote, $_POST, $person);
				$quote->save();
				getSystem()->flash('Citatet uppdaterades!', 'success')->redirect('adminQuotes');
			} catch(\Exception $e) {
				getSystem()->getRender()->error(500, 'Could not update quote', $e);
			}
		}

		public function quotesCreateActionPost($params = null) {
			$person = $this->getPerson();
			$quote = new \App\Quote;
			try {
				$this->setQuoteParams($quote, $_POST, $person);
				$quote->save();
				getSystem()->flash('Citatet skapades', 'success')->redirect('adminQuotes');
			} catch(\Exception $e) {
				getSystem()->getRender()->error(500, 'Could not save new quote', $e);
			}
		}

		public function quotesDeletePostAction($params) {
			$quote = new \App\Quote;
			try {
				$quote->load($params['qId']);
				//$quote->setParams(array('id' => $params['qId']));
				$quote->delete();
			} catch(\Exception $e) {
				getSystem()->getRender()->error(500, 'Could not delete quote: ' . $e->getMessage(), $e);
			}
		}

		private function setQuoteParams(\App\Quote $quote, $params, \App\Person $person) {
			$newParams = array(
				'quote' => $params['quote'],
				'quoteDate' => $params['quoteDate'],
				'source' => $params['source'],
				'dateAdded' => $quote->dateAdded,
				'comment' => $params['comment'],
				'person' => $person
			);
			$quote->setParams($newParams);
		}
	}