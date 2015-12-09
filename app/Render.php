<?php
	namespace App;

	class Render {

		public function render($file, $vars = []) {
			$jade = new \Jade\Jade;
			echo $jade->render('views/' . $file . '.jade', $vars);
		}

	}