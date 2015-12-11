<?php
	namespace App;
	use App\Render;
	class System {

		private $router;
		private $render;
		private $db;

		public function __construct() {
			$this->loadEnv();
			$this->compileLess();
		}
		
		public function setRouter(\AltoRouter $router) {
			$this->router = $router;
		}

		public function getRouter() {
			return $this->router;
		}

		public function getRender() {
			if(!$this->render)
				$this->render = new Render;
			return $this->render;
		}

		public function getDb() {
			if(!$this->db)
				$this->db = new Db();
			return $this->db;
		}

		public function renderRouterMatch() {
			$match = $this->router->match();

			// Match found
			if ($match) {
				if(is_callable( $match['target'] ) )
					call_user_func_array( $match['target'], $match['params'] ); 
				
				elseif(list($controller, $action) = explode( '#', $match['target'] )) {
					if ( is_callable(array($controller, $action)) )
						call_user_func_array(array($controller,$action), array($match['params']));
					else
						$this->render->error(500, 'Could not call ' . $controller . '#' . $action);
				}

				else
					$this->render->error(500, 'Matching route found but could not execute action');
			}
			else
				$this->render->error(404);
		}

		private function loadEnv() {
			try {
				$this->env = (new \josegonzalez\Dotenv\Loader('.env'))
							->parse()
							->putenv();
				} catch(\InvalidArgumentException $e) {
					$this->getRender()->error(500, 'Failed to open envirnomental file. Make sure it exists in app roop (/.env)', $e);
					die();
				}
		}

		private function compileLess() {
			$less = new \lessc;
			try {
				$less->checkedCompile(__DIR__ . "/../resources/less/main.less", __DIR__ . "/../resources/css/main.css");
			} catch(\Exception $e) {
				$this->render->error(500, $e->getMessage());
			}
		}

	}