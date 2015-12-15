<?php
	
	namespace App\Controllers;
	use \App\Render;

	class AboutController {

		public static function indexAction($params) {
			getSystem()->render('about');
		}

	}