<?php
	namespace App;
	use App\Render;
	class System {

		private $router;
		
		public function setRouter(\AltoRouter $router) {
			$this->router = $router;
		}

		public function getRouter() {
			return $this->router;
		}

		public function renderRouterMatch() {
			$match = $this->router->match();
			$render = new Render();

			// Match found
			if ($match) {
				if(is_callable( $match['target'] ) )
					call_user_func_array( $match['target'], $match['params'] ); 
				
				elseif(list($controller, $action) = explode( '#', $match['target'] )) {
					if ( is_callable(array($controller, $action)) )
						call_user_func_array(array($controller,$action), array($match['params']));
					else
						echo "500 Server Error. Could not call " . $controller . "#" . $action;
				}
				
				else
					echo "500 Server Error. Matching route found but could not execute action";
			}
			else
				$render->render('404');
		}

	}