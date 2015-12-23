<?php
	namespace App;

	class Quote {

		private $id, $loaded, $saved;
		public $quote, $source, $quoteDate, $dateAdded, $comment, $person;

		public function __construct() {
			$this->loaded = false;
		}

		public function setParams($params) {
			$this->instantiate($params);
		}

		private function instantiate($data) {
			if($data['id'])
				$this->id = $data['id'];
			$this->quote = $data['quote'];
			$this->source = $data['source'];
			$this->quoteDate = $data['quoteDate'];
			
			if(!$data['dateAdded'])
				$this->dateAdded = date("Y-m-d H:i:s");
			else
				$this->dateAdded = $data['dateAdded'];

			$this->comment = $data['comment'];
			$this->person = $data['person'];
		}

		public function getId() {
			return $this->id;
		}

		public function loadPrev($id) {
			echo "Loading prev";
			$this->load($id, 'prev');
		}

		public function loadNext($id) {
			echo "Loading next";
			$this->load($id, 'next');
		}

		public function load($id, $target = null) {
			$db = getSystem()->getDb();
			if($target == 'prev')
				$query = "SELECT * FROM quotes WHERE id = (SELECT max(id) FROM quotes WHERE id < ?)";
			elseif($target == 'next')
				$query = "SELECT * FROM quotes WHERE id = (SELECT min(id) FROM quotes WHERE id > ?)";
			else
				$query = "SELECT * FROM quotes WHERE id = ? LIMIT 1";

			$stmt = $db->prepare($query);
			if(!$stmt)
				throw new \Exception("Failed to prepare load quote (" . $db->errno . "): " . $db->error);
			$stmt->bind_param("i", $id);
			$stmt->execute();
			if(!$stmt)
				throw new \Exception("Failed to execute load quote (" . $db->errno . "): " . $db->error);

			$result = $stmt->get_result();
			if($result->num_rows === 1) {
				$this->loaded = true;
				$res = $result->fetch_assoc();
				$this->instantiate($res);
				$this->setPerson($res['person']);
				return $this;
			} else {
				if($target == 'next' || $target == 'prev')
					$this->load($id);
				else	
					throw new \Exception("No such quote (id: " . $id . ", query: " . $query . ")");
			}
		}

		public function save() {
			if($this->loaded)
				$query = "UPDATE";
			else
				$query = "INSERT INTO";

			$query .= " quotes SET quote=?, source=?, quoteDate=?, dateAdded=?, comment=?, person=?";

			if($this->loaded)
				$query .= " WHERE id=?";

			//echo $query;

			$db = getSystem()->getDb();
			$stmt = $db->prepare($query);
			if(!$stmt)
				throw new \Exception("Failed to prepare save quote (" . $db->errno . "): " . $db->error);
			$this->bindParams($stmt);
			$stmt->execute();
			if(!$stmt || $stmt->error)
				throw new \Exception("Failed to execute save quote (" . $db->errno . "): " . $db->error);

			if(!$this->loaded) {
				$this->id = $db->insert_id;
				$this->loaded = true;
				$this->saved = true;
			}
			return $this;
		}

		public function delete() {
			$db = getSystem()->getDb();
			$delete = $db->prepare("DELETE FROM quotes WHERE id = ? LIMIT 1");
			if(!$delete)
				throw new \Exception("Failed to prepare delete quote (" . $db->errno . "): " . $db->error);
			$delete->bind_param("i", $this->id);
			$delete->execute();
			if(!$delete || $delete->error)
				throw new \Exception("Failed to execute delete quote (" . $db->errno . "): " . $db->error);

			return true;
		}

		public function toArray() {
			$re =  array(
				'id' => $this->id,
				'quote' => $this->quote,
				'source' => $this->source,
				'quoteDate' => $this->quoteDate,
				'dateAdded' => $this->dateAdded,
				'comment' => $this->comment,
				'person' => $this->person->toArray(),
				'loaded' => $this->loaded,
				'saved' => $this->saved,
			);
			if (filter_var($this->source, FILTER_VALIDATE_URL)) {
				$re['sourceIsLink'] = true;
				$re['sourceParts'] = parse_url($this->source);
			} else
				$re['sourceIsLink'] = false;

			return $re;
		}

		private function bindParams($stmt) {
			if($this->loaded)
				$stmt->bind_param("sssssii", $this->quote, $this->source, $this->quoteDate, $this->dateAdded, $this->comment, $this->person->getId(), $this->id);
			else
				$stmt->bind_param("sssssi", $this->quote, $this->source, $this->quoteDate, $this->dateAdded, $this->comment, $this->person->getId());
		}

		private function setPerson($personId) {
			$p = new \App\Person();
			$person = $p->load($personId);
			$this->person = $person;
			return $person;
		}

		public static function listQuotes() {
			$db = getSystem()->getDb();
			$q = $db->query("SELECT q.id AS quoteId,
									q.quote, 
									q.source, 
									q.quoteDate, 
									q.dateAdded, 
									q.comment, 
									p.id AS personId, 
									p.name, 
									p.position, DATE_FORMAT(dateAdded, '%d/%c %H:%i') as formattedSubmitDate FROM quotes AS q LEFT JOIN persons AS p ON q.person = p.id ORDER BY dateAdded DESC");
			if(!$q)
				throw new \Exception("Error fetching list of quotes (" . $db->errno . "): " . $db->error);

			$arr = $q->fetch_all(MYSQLI_ASSOC);
			$q->free();
			return $arr;
		}

		public static function getRandomQuoteId() {
			$db = getSystem()->getDb();
			$q = $db->query("SELECT id FROM quotes ORDER BY RAND() LIMIT 1");
			if(!$q)
				throw new \Exception("Error fetching random quote ID (" . $db->errno . "): " . $db->error);
			$arr = $q->fetch_all(MYSQLI_ASSOC);
			$q->free();
			return $arr[0]['id'];
		}

	}