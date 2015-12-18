<?php

	namespace App;

	class Auth {

		public $userName;
		private $userId;

		public function isAuthenticated() {
			if(isset($_SESSION['sdLogedIn']))
				return true;
			else
				return false;
		}

		public function getProtectedRoutes() {
			return array('admin', 'adminApprove');
		}

		public function authenticate($user, $password) {
			$db = getSystem()->getDb();
			$userCheck = $db->prepare('SELECT id, user FROM users WHERE user = ? AND password = ?');
			if(!$userCheck)
				throw new \Exception("Error preparing authentication (" . $db->errno . "): " . $db->error);

			$userCheck->bind_param('ss', $user, $this->generatePassword($user, $password));
			$userCheck->execute();
			if(!$userCheck)
				throw new \Exception("Error when authenticating (" . $db->errno . "): " . $db->error);
			$userCheck->bind_result($userId, $userName);
			$userCheck->store_result();

			if($userCheck->num_rows === 1) {
				$this->userName = $userName;
				$this->userId = $userId;
				return true;
			} else
				return false;
		}

		private function generatePassword($user, $password) {
			return md5($user . getenv('AUTH_SALT') . md5($password));
		}

		public function persist() {
			$_SESSION['sdLogedIn'] = true;
			$_SESSION['sdLogedInUser'] = $this->userName;
		}

		// TEMP
		public function printPsw($user, $password) {
			return $this->generatePassword($user, $password);
		}

	}