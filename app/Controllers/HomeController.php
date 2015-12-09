<?php
	
	namespace App\Controllers;

	class HomeController {

		public function indexAction($params) {
			echo "This is the test page";
			echo "<hr>";
			print_r($params);
		}
	}