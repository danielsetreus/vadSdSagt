<?php
	
	namespace App;

	class SuggestedQuote {

		private $id;
		public $quote;
		public $person;
		public $position;
		public $date;
		public $source;

		private $isInstantiated;
		private $isLoaded;

		public function __construct($data = null) {
			if($data) {
				$this->isLoaded = false;
				$this->instantiate($data);
			}
		}

		private function instantiate($data) {
			if($data['id'])
				$this->id = $data['id'];
			
			$this->quote = $data['quote'];
			$this->person = $data['person'];
			$this->position = $data['position'];
			$this->date = $data['date'];
			$this->source = $data['source'];

			$this->isInstantiated = true;
		}

		public function save() {
			if($this->isInstantiated) {
				if($this->isLoaded) {
					echo "Trying to update record";
				} else {
					// Create new record
					$this->create();
				}
			} else
				throw new \Exception("SuggestedQuote object has not been instatiated. Save cannot be made.");		
		}

		private function create() {
			$db = getSystem()->getDb();
			$creation = $db->prepare('INSERT INTO suggestedQuotes 
				(quote, person, position, quoteDate, source, submitDate) VALUES(
					?, ?, ?, ?, ?, ?
				)');
			if(!$creation)
				throw new \Exception("Database error when preparing new SuggestQuote (" . $db->errno . "): " . $db->error);
			else {

				$creation->bind_param('ssssss', 
					$db->escape_string($this->quote),
					$db->escape_string($this->person),
					$db->escape_string($this->position),
					$db->escape_string($this->date),
					$db->escape_string($this->source),
					date("Y-m-d H:i:s")
				);

				$stmt = $creation->execute();

				if(!$stmt)
					throw new \Exception("Database error when creating new SuggestQuote (" . $db->errno . "): " . $db->error);
				else
					getSystem()->redirect('suggestThanks');
			}

		}

		public static function listSuggestedQuotes() {
			$db = getSystem()->getDb();
			$sq = $db->query("SELECT *, DATE_FORMAT(submitDate, '%d/%c %H:%i') as formattedSubmitDate FROM suggestedQuotes ORDER BY submitDate DESC");
			if(!$sq)
				throw new \Exception("Error fetching list of suggested quotes (" . $db->errno . "): " . $db->error);

			$arr = $sq->fetch_all(MYSQLI_ASSOC);
			$sq->free();
			$db->close();
			return $arr;
		}

	}