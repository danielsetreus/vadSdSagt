<?php
	
	namespace App\Controllers;
	use \App\Render;

	class HomeController {

		public static function indexAction($params) {
			$system = getSystem();
			$db = $system->getDb();

			//self::testDb();

			$render = new Render;
			$render->render('home');
		}

		private function testDb() {
			$db = getSystem()->getDb();
			$db->query("SELECT * FROM quotes");
		}
	}