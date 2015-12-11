<?php
	namespace App;
	use \mysqli;

	class Db extends \mysqli {

		private $connection;
		private $system;

		public function __construct() {
			$this->system = getSystem();
			@$this->conection = parent::__construct(getenv('DB_HOST'), getenv('DB_USER'), getenv('DB_PSW'), getenv('DB_NAME'));
			
			if (mysqli_connect_errno()) {
				$this->system->getRender()->error(500, 'Error connecting to database: ' . mysqli_connect_error());
				die();
			}
			parent::set_charset("utf8");
		}

		public function query($str) {
			$res = parent::query($str);
			if (!$res) {
				$this->system->getRender()->error(500, 'Error when executing query to database: ' . $this->error);
				die();
			}
			return $res;
		}

	}
