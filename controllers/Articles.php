<?php

class Articles extends Controller {
	/**
	 * Define an array of models that are required.
	 */
	public $useModels = [
		'Article',
		'User'
	];

	/**
	 * Define an array of authorization permissions.
	 */
	public $permissions = [
		'admin' => [
			'setting' => ['level' => [2]]
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

	/**
	 * Browse articles.
	 */
	public function index(){
		$articleModel = new Article();
		$userModel = new User();
		$articles = $articleModel->findAll([], ['orderBy' => ['created', 'DESC']]);
		foreach($articles as $key => $article){
			$user = $userModel->findOne($article['user_id']);
			$articles[$key]['author'] = $user->username;
		}
		$this->view('index', ['articles' => $articles]);
	}

	/**
	 * Admin view of articles.
	 */
	public function admin($data){
		// Create array of all users for a user select dropdown.
		$userModel = new User();
		$content = [];
		$selectedUser = NULL;
		$users = $userModel->findAll([], ['orderBy' => ['username', 'ASC']]);
		foreach($users as $user){
			// Take first found user as selecteduser.
			if(is_null($selectedUser)){
				$selectedUser = $user['user_id'];
			}
			$content[$user['user_id']] = $user['username'];
		}
		// Get all articles.
		$articleModel = new Article();
		// If user has been selected from dropdown,
		if($this->methodPost()){
			$selectedUser = $data['username'];
		}
		$articles = $articleModel->findAll(['user_id' => $selectedUser], ['orderBy' => ['created', 'DESC']]);
		foreach($articles as $key => $article){
			$thisUser = $userModel->findOne($article['user_id']);
			$articles[$key]['author'] = $thisUser->username;
		}
		$this->view('admin', ['articleModel' => $articleModel, 'articles' => $articles, 'userModel' => $userModel, 'content' => $content, 'selectedUser' => $selectedUser]);
	}

	public function article($data){
		$articleModel = new Article();
		$article = $articleModel->findOne($data['article_id']);
		$userModel = new User();
		$user = $userModel->findOne($article->user_id);
		$article->author = $user->username;
		$this->view('article', ['article' => $article]);
	}

	/**
	 * List of user's articles.
	 */
	public function myArticles(){
		$articleModel = new Article();
		$articles = $articleModel->findAll(['user_id' => Session::getKey('user_id')], ['orderBy' => ['created', 'DESC']]);
		$this->view('myarticles', ['model' => $articleModel, 'articles' => $articles]);
	}

	/**
	 * Write new article.
	 */
	public function write($data){
		$articleModel = new Article();
		if($this->methodPost()){

			$articleModel->values($data);
			if($articleModel->validate()){
				if($articleModel->save()){
					SESSION::setFlash('success', 'Article saved.');
					$this->redirect('articles', 'myarticles');
					exit;
				} else {
					SESSION::setFlash('error', 'Could not save article.');
					$this->redirect('articles', 'myarticles');
				}
			}
		}
		$this->view('write', ['model' => $articleModel, 'useEditor' => true]);
	}

	/**
	 * Edit an article.
	 */
	public function edit($data){
		$articleModel = new Article();
		if($this->methodPost()){
			$articleModel->values($data);
			if($articleModel->validate()){
				if($articleModel->save()){
					SESSION::setFlash('success', 'Article updated.');
					$this->redirect('articles', 'admin');
					exit;
				}
			}
		} else {
			$article = $articleModel->findOne($data['article_id']);
			$articleModel->values($article);
		}
		$this->view('edit', ['model' => $articleModel, 'useEditor' => true]);
	}

	/**
	 * Delete article.
	 */
	public function delete($data){
		if($this->methodGet()){
			$articleModel = new Article();
			if($articleModel->delete($data['article_id'])){
				SESSION::setFlash('success', 'Article deleted.');
				$this->redirect('articles', 'admin');
				exit;
			} else {
				$this->redirect('articles', 'admin');
			}
		}
	}
}
?>