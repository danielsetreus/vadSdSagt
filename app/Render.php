<?php
	namespace App;

	class Render {

		public function status($code) {
			http_response_code($code);
			return $this;
		}

		public function render($file, $vars = []) {
			$jade = new \Jade\Jade;
			echo $jade->render('views/' . $file . '.jade', $vars);
		}

		public function error($err, $msg = null, \Exception $e = null) {

			if(!empty($msg))
				$msgVars = array('errorMessage' => $msg);
			else
				$msgVars = array('errorMessage' => 'Unknown error');

			if(getenv('DEBUG') && $e) {
				$msgVars['exception'] = $e;
				$msgVars['backtrace'] = print_r(debug_backtrace(), TRUE);
			}

			$this->status($err)->render($err, $msgVars);
		}

	}