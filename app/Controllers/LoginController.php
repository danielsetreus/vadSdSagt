<?php
	
	namespace App\Controllers;
	use \App\Auth;
	use \App\Render;

	class LoginController {

		public function indexAction($params) {
			getSystem()->render('loginForm');
		}

		public function loginAction($params = null) {
			if(isset($_POST['doLogin'])) {
				$auth = getSystem()->getAuth();
				try {
					if($auth->authenticate($_POST['user'], $_POST['password'])) {
						$auth->persist();
						getSystem()->redirect('admin');
					} else {
						getSystem()->render('loginForm', array('loginError' => true));
					}
				} catch(\Exception $e) {
					getSystem()->getRender()->error(500, 'Failed when trying to authenticate', $e);
				}
			}
		}

	}