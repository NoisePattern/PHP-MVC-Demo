<?php

class Users extends Controller {
	/**
	 * Define an array of models that are required.
	 */
	public $useModels = [
		'User',
		'LoginForm'
	];

	/**
	 * Define an array of authorization permissions.
	 */
	public $permissions = [];

	/**
	 * Login action.
	 *
	 * @param array $data Request parameters.
	 */
	public function login($data){
		$loginForm = new LoginForm();
		if($this->methodPost()){
			$loginForm->values($data);
			if($loginForm->validate()){
				if($loginForm->login()){
					$this->redirect('articles', 'index');
					exit;
				} else {
					$this->redirect('users', 'login');
					exit;
				}
			}
		}
		$this->view('login', ['model' => $loginForm]);
	}

	/**
	 * Logout action.
	 */
	public function logout(){
		if(ini_get("session.use_cookies")){
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				'',
				time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
		session_destroy();
		$this->redirect('users', 'login');
	}

	/**
	 * Register action.
	 *
	 * @param array $data Request params.
	 */
	public function register($data){
		$userModel = new User();
		if($this->methodPost()){
			$userModel->values($data);
			if($userModel->validate()){
				if($userModel->save()){
					SESSION::setFlash('success', 'Registration successful.');
					$this->redirect('users', 'login');
					exit;
				}
			}
		}
		$this->view('register', ['model' => $userModel]);
	}
}

?>