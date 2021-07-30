<?php

class Articles extends Controller {
	/**
	 * Define an array of models that are required.
	 */
	public function useModels(){
		return [
			'Article',
			'User'
		];
	}

	/**
	 * Define an array of authorization permissions.
	 */
	public function permissions(){
		return [
			'admin' => [
				'setting' => ['level' => [2]]
			],
			'myarticles' => [
				'login' => true
			],
			'write' => [
				'login' => true
			],
			'edit' => [
				'login' => true
			],
			'delete' => [
				'login' => true
			]
		];
	}

	/**
	 * Browse articles.
	 */
	public function index(){
		$articleModel = new Article();
		$userModel = new User();
		$articles = $articleModel->findAll(['published' => 1], ['orderBy' => ['created', 'DESC']]);
		foreach($articles as $key => $article){
			$user = $userModel->findOne($article['user_id']);
			$articles[$key]['author'] = $user->username;
		}
		$this->view('index', ['articles' => $articles]);
	}

	/**
	 * Admin view of articles.
	 */
	public function admin(){
		// Create array of all users for a user select dropdown.
		$userModel = new User();
		$dropdownContent = [];
		$selectedUser = NULL;
		$users = $userModel->findAll([], ['orderBy' => ['username', 'ASC']]);
		foreach($users as $user){
			// Take first found user as selected user.
			if(is_null($selectedUser)){
				$selectedUser = $user['user_id'];
			}
			$dropdownContent[$user['user_id']] = $user['username'];
		}
		// Get all articles.
		$articleModel = new Article();
		// If user has been selected from dropdown.
		if(Application::$app->request->isPost()){
			$data = Application::$app->request->post();
			$selectedUser = $data['selectedUser'];
		}
		$userModel->selectedUser = $selectedUser;
		$articles = $articleModel->findAll(['user_id' => $selectedUser], ['orderBy' => ['created', 'DESC']]);
		foreach($articles as $key => $article){
			$thisUser = $userModel->findOne($article['user_id']);
			$articles[$key]['author'] = $thisUser->username;
		}
		$this->view('admin', ['articleModel' => $articleModel, 'articles' => $articles, 'userModel' => $userModel, 'dropdownContent' => $dropdownContent]);
	}

	public function article(){
		$articleModel = new Article();
		$data = Application::$app->request->get();
		$article = $articleModel->findOne($data['article_id']);
		if($article){
			$userModel = new User();
			$user = $userModel->findOne($article->user_id);
			$article->author = $user->username;
			$this->view('article', ['article' => $article]);
		} else {
			Application::$app->request->redirect('articles', 'index');
		}
	}

	/**
	 * List of user's articles.
	 */
	public function myarticles(){
		$articleModel = new Article();
		$articles = $articleModel->findAll(['user_id' => Session::getKey('user_id')], ['orderBy' => ['created', 'DESC']]);
		$this->view('myarticles', ['model' => $articleModel, 'articles' => $articles]);
	}

	/**
	 * Write new article.
	 */
	public function write(){
		$articleModel = new Article();
		if(Application::$app->request->isPost()){
			$articleModel->values(Application::$app->request->post());
			if($articleModel->validate()){
				if($articleModel->save()){
					SESSION::setFlash('success', 'Article saved.');
					Application::$app->request->redirect('articles', 'myarticles');
					exit;
				} else {
					SESSION::setFlash('error', 'Could not save article.');
					Application::$app->request->redirect('articles', 'myarticles');
				}
			}
		}
		$this->view('write', ['model' => $articleModel, 'useEditor' => true]);
	}

	/**
	 * Edit an article.
	 */
	public function edit(){
		// Multiple pages can send to edit action, redirect must send back to correct page.
		$sender = Application::$app->request->getReferer();
		// If sender was edit page, it was due to validation failing the previous time. If so, do not update sender.
		if($sender !== false && $sender['action'] !== 'edit'){
			Session::setKey(['senderController' => $sender['controller'], 'senderAction' => $sender['action']]);
		}
		$articleModel = new Article();
		if(Application::$app->request->isPost()){
			$articleModel->values(Application::$app->request->post());
			if($articleModel->validate()){
				if($articleModel->save()){
					Session::setFlash('success', 'Article updated.');
					if(Session::checkKey('senderController') && Session::checkKey('senderAction')){
						Application::$app->request->redirect(Session::getKey('senderController'), Session::getKey('senderAction'));
						Session::unsetKey(['senderController', 'senderAction']);
					} else {
						Application::$app->request->redirect('articles', 'myarticles');
					}
					exit;
				}
			}
		} else {
			$data = Application::$app->request->get();
			$article = $articleModel->findOne($data['article_id']);
			$articleModel->values($article);
		}
		$this->view('edit', ['model' => $articleModel, 'useEditor' => true]);
	}

	/**
	 * Delete article.
	 */
	public function delete(){
		// Multiple pages can send to delete action, redirect must send back to correct page.
		$sender = Application::$app->request->getReferer();
		if(Application::$app->request->isGet()){
			$data = Application::$app->request->get();
			$articleModel = new Article();
			if($articleModel->delete($data['article_id'])){
				Session::setFlash('success', 'Article deleted.');
			} else {
				Session::setFlash('error', 'Could not delete article.');
			}
		}
		if($sender){
			Application::$app->request->redirect($sender['controller'], $sender['action']);
		} else {
			Application::$app->request->redirect('articles', 'myarticles');
		}
	}
}
?>