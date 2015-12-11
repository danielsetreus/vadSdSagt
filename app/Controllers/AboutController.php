<?php
	
	namespace App\Controllers;
	use \App\Render;

	class AboutController {

		public static function indexAction($params) {
			$render = new Render;
			$render->render('about');
		}

	}