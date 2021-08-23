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
		$pageSize = 5;						// Number of articles per page.
		$page = 0;							// Current page.
		$articleModel = new Article();
		$userModel = new User();

		// If pagenav link was clicked.
		if(Application::$app->request->isGet()){
			$data = Application::$app->request->get();
			if(isset($data['page'])) $page = $data['page'];
		}

		// Get articles for current page.
		$articles = $articleModel->findAll(['published' => 1], ['limit' => $pageSize, 'offset' => $pageSize * $page, 'orderBy' => ['created', 'DESC']]);
		// Get number of published articles
		$total = $articleModel->count(['published' => 1]);

		$this->view('index', ['articles' => $articles, 'pageSize' => $pageSize, 'page' => $page, 'total' => $total]);
	}

	/**
	 * Admin view of articles.
	 */
	public function admin(){
		$selectedUser = 0;			// Selected user.
		$pageSize = 10;				// Number of articles per page.
		$page = 0;					// Current page.

		// Check if article edit page wants to return to list of specific user's articles.
		if(Session::checkKey('selectedUser')){
			$selectedUser = Session::getKey('selectedUser');
			Session::unsetKey(['selectedUser']);
		}
		// If user has been selected from dropdown.
		if(Application::$app->request->isPost()){
			$data = Application::$app->request->post();
			if(isset($data['selectedUser'])) $selectedUser = $data['selectedUser'];
		}
		// If pagenav link was clicked.
		else if(Application::$app->request->isGet()){
			$data = Application::$app->request->get();
			if(isset($data['selectedUser'])) $selectedUser = $data['selectedUser'];
			if(isset($data['page'])) $page = $data['page'];
		}

		// Create array of all users for a user selection dropdown.
		$userModel = new User();
		$dropdownContent[] = ['text' => 'All users', 'options' => ['value' => '0']];
		$users = $userModel->findAll([], ['orderBy' => ['username', 'ASC']]);
		foreach($users as $user){
			$dropdownContent[] = ['text' => $user['username'], 'options' => ['value' => $user['user_id']]];
		}
		$userModel->selectedUser = $selectedUser;

		// Get user's articles on given page.
		$articleModel = new Article();
		$where = [];
		if($selectedUser != 0) $where['user_id'] = $selectedUser;
		$articles = $articleModel->findAll($where, ['limit' => $pageSize, 'offset' => $page * $pageSize, 'orderBy' => ['created', 'DESC']]);
		// Get total number of articles by user (or all users).
		$total = $articleModel->count($where);

		$this->view('admin', ['articleModel' => $articleModel, 'articles' => $articles, 'userModel' => $userModel, 'dropdownContent' => $dropdownContent,
		'page' => $page, 'pageSize' => $pageSize, 'total' => $total
		]);
	}

	/**
	 * Display article.
	 */
	public function article(){
		$articleModel = new Article();
		$data = Application::$app->request->get();
		$article = $articleModel->findOne($data['article_id'], PDO::FETCH_OBJ);
		if($article){
			$userModel = new User();
			$user = $userModel->findOne($article->user_id);
			$article->author = $user['username'];
			$this->view('article', ['article' => $article]);
		} else {
			Application::$app->request->redirect('articles', 'index');
		}
	}

	/**
	 * List of user's articles.
	 */
	public function myarticles(){
		$pageSize = 10;				// Number of articles per page.
		$page = 0;					// Current page.

		// If pagenav link was clicked.
		if(Application::$app->request->isGet()){
			$data = Application::$app->request->get();
			if(isset($data['page'])) $page = $data['page'];
		}
		$articleModel = new Article();
		$articles = $articleModel->findAll(['user_id' => Session::getKey('user_id')], ['limit' => $pageSize, 'offset' => $page * $pageSize, 'orderBy' => ['created', 'DESC']]);

		// Get total number of articles by user (or all users).
		$total = $articleModel->count(['user_id' => Session::getKey('user_id')]);

		$this->view('myarticles', ['model' => $articleModel, 'articles' => $articles, 'pageSize' => $pageSize, 'page' => $page, 'total' => $total]);
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
						// If page that sent to edit was admin page, inform it of user to select from dropdown.
						if(Session::getKey('senderAction') == 'admin'){
							Session::setKey(['selectedUser' => $articleModel->user_id]);
						}
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
		$options = [];
		if(Application::$app->request->isGet()){
			$data = Application::$app->request->get();
			$articleModel = new Article();
			if($articleModel->delete($data['article_id'])){
				Session::setFlash('success', 'Article deleted.');
			} else {
				Session::setFlash('error', 'Could not delete article.');
			}
			// If link contained user selection data, forward it to redirected page.
			if(isset($data['selectedUser'])) $options['selectedUser'] = $data['selectedUser'];
		}
		if($sender){
			Application::$app->request->redirect($sender['controller'], $sender['action'], $options);
		} else {
			Application::$app->request->redirect('articles', 'myarticles', $options);
		}
	}
}
?>