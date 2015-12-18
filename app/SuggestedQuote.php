<?php
	
	namespace App;

	class SuggestedQuote {

		private $id, $deleted;
		public $quote;
		public $person;
		public $position;
		public $quoteDate;
		public $source;
		public $submitDate;

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
			$this->quoteDate = $data['quoteDate'];
			$this->source = $data['source'];

			if($data['deleted'])
				$this->deleted = $data['deleted'];

			if($data['submitDate'])
				$this->submitDate = $data['submitDate'];

			$this->isInstantiated = true;
		}

		public function toArray() {
			$re = array(
				'id' => $this->id,
				'quote' => $this->quote,
				'person' => $this->person,
				'position' => $this->position,
				'quoteDate' => $this->quoteDate,
				'source' => $this->source,
				'submitDate' => $this->submitDate,
				'deleted' => $this->deleted
			);
			return $re;
		}

		public function load($sqId) {
			$db = getSystem()->getDb();
			$sq = $db->prepare("SELECT * FROM suggestedQuotes WHERE id = ?");
			if(!$sq)
				throw new \Exception("Failed to prepare load (" . $db->errno . "): " . $db->error);
			
			$sq->bind_param('i', $sqId);
			
			if(!$sq->execute())
				throw new \Exception("Failed to load (" . $db->errno . "): " . $db->error);
			
			$result = $sq->get_result();
			if($result->num_rows === 1) {
				$this->instantiate($result->fetch_assoc());
				$this->loaded = true;
				return $this;
			} else {
				return false;
			}
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
					$db->escape_string($this->quoteDate),
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

		public function softDelete() {
			$db = getSystem()->getDb();
			$soft = $db->prepare('UPDATE suggestedQuotes SET deleted = 1 WHERE id = ? LIMIT 1');
			if(!$soft)
				throw new \Exception("Database error when preparing soft delete SuggestQuote (" . $db->errno . "): " . $db->error);
			$soft->bind_param("i", $this->id);
			$soft->execute();
			if(!$soft)
				throw new \Exception("Database error when executing soft delete SuggestQuote (" . $db->errno . "): " . $db->error);
			return $this;
		}
		public function delete() {
			if(!$this->isInstantiated)
				throw new \Exception("Suggested Question not yet instantiated");

			$db = getSystem()->getDb();
			$deletion = $db->prepare('DELETE FROM suggestedQuotes WHERE id = ? LIMIT 1');
			if(!$deletion)
				throw new \Exception("Database error when preparing delete SuggestQuote (" . $db->errno . "): " . $db->error);
			$deletion->bind_param("i", $this->id);
			$deletion->execute();
			if(!$deletion)
				throw new \Exception("Database error when executing delete SuggestQuote (" . $db->errno . "): " . $db->error);
			$this->isLoaded = false;
			return $this;
		}

		public static function listSuggestedQuotes() {
			$db = getSystem()->getDb();
			$sq = $db->query("SELECT *, DATE_FORMAT(submitDate, '%d/%c %H:%i') as formattedSubmitDate FROM suggestedQuotes WHERE deleted = 0 ORDER BY submitDate DESC");
			if(!$sq)
				throw new \Exception("Error fetching list of suggested quotes (" . $db->errno . "): " . $db->error);

			$arr = $sq->fetch_all(MYSQLI_ASSOC);
			$sq->free();
			$db->close();
			return $arr;
		}

	}