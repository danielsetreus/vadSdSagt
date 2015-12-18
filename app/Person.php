<?php

	namespace App;

	class Person {

		private $id, $loaded;
		public $name, $position, $image;

		public function __construct() {
			$this->loaded = false;
		}

		public function setParams($data) {
			$this->instantiate($data);
		}

		public function getId() {
			return $this->id;
		}

		public function load($id) {
			$db = getSystem()->getDb();
			$stmt = $db->prepare("SELECT * FROM persons WHERE id = ? LIMIT 1");
			if(!$stmt)
				throw new \Exception("Failed to prepare load person (" . $db->errno . "): " . $db->error);
			$stmt->bind_param("i", $id);
			$stmt->execute();
			if(!$stmt)
				throw new \Exception("Failed to execute load person (" . $db->errno . "): " . $db->error);

			$result = $stmt->get_result();
			if($result->num_rows === 1) {
				$this->loaded = true;
				$this->instantiate($result->fetch_assoc());
				return $this;
			} else
				throw new \Exception("No such person");
		}

		public function save() {
			if($this->loaded)
				$query = "UPDATE";
			else
				$query = "INSERT INTO";

			$query .= " persons SET name=?, position=?, image=?";

			if($this->loaded)
				$query .= " WHERE id=?";

			$db = getSystem()->getDb();
			$stmt = $db->prepare($query);
			if(!$stmt)
				throw new \Exception("Failed to prepare save person (" . $db->errno . "): " . $db->error);
			$this->bindParams($stmt);
			$stmt->execute();
			if(!$stmt || $stmt->error)
				throw new \Exception("Failed to execute save person (" . $db->errno . "): " . $db->error);

			if(!$this->loaded) {
				$this->id = $db->insert_id;
				$this->loaded = true;
			}
			return $this;
		}

		public function toArray() {
			return array(
				'id' => $this->id,
				'name' => $this->name,
				'position' => $this->position,
				'image' => $this->image
			);
		}

		private function bindParams($stmt) {
			if($this->loaded)
				$stmt->bind_param("sssi", $this->name, $this->position, $this->image, $this->id);
			else
				$stmt->bind_param("sss", $this->name, $this->position, $this->image);
		}

		private function instantiate($data) {
			if($data['id'])
				$this->id = $data['id'];
			
			$this->name = $data['name'];
			$this->position = $data['position'];
			$this->image = $data['image'];
		}

		public static function search($name) {
			$db = getSystem()->getDb();
			$stmt = $db->prepare("SELECT * FROM persons WHERE name LIKE ?");
			if(!$stmt)
				throw new \Exception("Could not prepare search query (" . $db->errno . ") " . $db->error);

			$stmt->bind_param("s", $name);
			$stmt->execute();
			if(!$stmt)
				throw new \Exception("Could not execute search query (" . $db->errno . ") " . $db->error);
			
			$result = $stmt->get_result();
			return $result->fetch_all(MYSQLI_ASSOC);
		}

		public static function getImageDir() {
			return array(
				'absolute' => dirname(__FILE__) . "/../resources/images/persons/",
				'relative' => '/resources/images/persons/'
			);
		}

	}