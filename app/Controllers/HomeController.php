<?php
	
	namespace App\Controllers;
	use \App\Render;

	class HomeController {

		public static function indexAction($params) {
			getSystem()->render('home');
		}

		private function testDb() {
			$db = getSystem()->getDb();
			$db->query("SELECT * FROM quotes");
		}
	}