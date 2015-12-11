<?php
	
	namespace App\Controllers;
	use \App\Render;

	class AboutController {

		public function indexAction($params) {
			$render = new Render;
			$render->render('about');
		}

	}