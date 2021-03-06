<?php

class Users extends Controller {
	/**
	 * Define an array of models that are required.
	 */
	public function useModels(){
		return [
			'User',
			'LoginForm'
		];
	}

	/**
	 * Login action.
	 *
	 * @param array $data Request parameters.
	 */
	public function login(){
		$loginForm = new LoginForm();
		if(Application::$app->request->isPost()){
			$loginForm->values(Application::$app->request->post());
			if($loginForm->validate()){
				if($loginForm->login()){
					session_regenerate_id();
					Application::$app->request->redirect('articles', 'index');
					exit;
				} else {
					Session::setFlashRender('error', 'Username or password is incorrect.');
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
		Application::$app->request->redirect('users', 'login');
	}

	/**
	 * Register action.
	 *
	 * @param array $data Request params.
	 */
	public function register(){
		$userModel = new User();
		if(Application::$app->request->isPost()){
			$userModel->values(Application::$app->request->post());
			// For now, just set role of all registered users to 1 (standard user).
			$userModel->role_id = 1;
			if($userModel->validate()){
				if($userModel->save()){
					SESSION::setFlash('success', 'Registration successful.');
					Application::$app->request->redirect('users', 'login');
					exit;
				}
			} else {
				var_dump($userModel->errors);
			}
		}
		$this->view('register', ['model' => $userModel]);
	}
}

?>