<?php

class Galleries extends Controller {
	/**
	 * Define an array of models that are required.
	 */
	public function useModels(){
		return [
			'Gallery',
			'GalleryImage',
			'User'
		];
	}

	/**
	 * Define an array of authorization permissions.
	 */
	public function permissions(){
		return [
			'galleryadmin' => [
				'setting' => ['level' => [2]]
			],
			'galleryadd' => [
				'setting' => ['level' => [2]]
			],
			'gallerydelete' => [
				'setting' => ['level' => [2]]
			],
			'imageadmin' => [
				'setting' => ['level' => [2]]
			],
			'myimages' => [
				'login' => true
			],
			'imageadd' => [
				'login' => true
			],
			'imagedelete' => [
				'login' => true
			]
		];
	}

	/**
	 * View gallery.
	 */
	public function index(){
		$selectedGallery = null;					// Gallery selection dropdown's current selection.
		$images = [];								// Images in selected gallery.
		$galleryModel = new Gallery();
		$imageModel = new GalleryImage();

		// Gallery selection from dropdown.
		if(Application::$app->request->isPost()){
			$data = Application::$app->request->post();
			if(isset($data['selectedGallery'])) $selectedGallery = $data['selectedGallery'];
		}

		// Create dropdown content of all published galleries.
		$allGalleries = $galleryModel->findAll(['public' => 1], ['orderBy' => ['name', 'asc']]);
		$selectOptions = $this->buildOptions($allGalleries, $selectedGallery, "&nbsp;&nbsp;&nbsp;&nbsp;");

		// Get images in selected gallery.
		if($selectedGallery){
			$gallery = $galleryModel->findOne($selectedGallery);
			$galleryModel->values($gallery);
			$images = $imageModel->findAll(['gallery_id' => $selectedGallery], ['orderBy' => ['created', 'desc']]);
		}
		$galleryModel->selectedGallery = $selectedGallery;

		$this->view('index', ['galleryModel' => $galleryModel, 'selectOptions' => $selectOptions,'images' => $images]);
	}

	/**
	 * Manage galleries.
	 */
	public function galleryadmin(){
		// Check permission.
		if(!Auth::checkUserPermission('manageGalleries')){
			Application::$app->request->redirect('users', 'login');
			exit;
		}

		$selectedGallery = null;					// Gallery selection dropdown's current selection.
		$galleryModel = new Gallery();

		// If post data was sent from either form on page.
		if(Application::$app->request->isPost()){
			$data = Application::$app->request->post();
			// If gallery selection form submit.
			if(array_key_exists('selectedGallery', $data)){
				// Set gallery as selected and read its data to model.
				$selectedGallery = $data['selectedGallery'];
				$gallery = $galleryModel->findOne($selectedGallery);
				$galleryModel->values($gallery);
			}
			// If gallery settings form submit.
			else if(array_key_exists('update', $data)){
				$galleryModel->values($data);
				$selectedGallery = $data['gallery_id'];
				if($galleryModel->validate()){
					if($galleryModel->save()){
						Session::setFlashRender('success', 'Gallery has been updated.');
					} else {
						Session::setFlashRender('error', 'Gallery could not be updated.');
					}
				}
			}
		}

		// Get all galleries and format them to tree structure.
		$allGalleries = $galleryModel->findAll([], ['orderBy' => ['name', 'asc']]);
		$selectOptions = $this->buildOptions($allGalleries, $selectedGallery, "&nbsp;&nbsp;&nbsp;&nbsp;");
		// Generate parent gallery changer select's content, omit the current gallery from choices.
		$parentSelectOptions[0] = ['text' => 'None', 'options' => ['value' => '']];
		$parentSelectOptions += $this->buildOptions($allGalleries, $galleryModel->parent_id, "&nbsp;&nbsp;&nbsp;&nbsp;", $selectedGallery);
		// Set selection to gallery select dropdown.
		$galleryModel->selectedGallery = $selectedGallery;
		$this->view(
			'galleryadmin',
			[
				'galleryModel' => $galleryModel,
				'selectOptions' => $selectOptions,
				'parentSelectOptions' => $parentSelectOptions
			]
		);
	}

	/**
	 * Manage images in gallery.
	 */
	public function imageadmin(){
		// Check permission.
		if(!Auth::checkUserPermission('manageAllImages')){
			Application::$app->request->redirect('users', 'login');
			exit;
		}

		$pageSize = 10;
		$page = 0;
		$total = 0;
		$selectedGallery = 0;
		$images = [];
		$galleryModel = new Gallery();
		$imageModel = new GalleryImage();

		// If pagenav link was clicked.
		if(Application::$app->request->isGet()){
			$data = Application::$app->request->get();
			if(isset($data['selectedGallery'])) $selectedGallery = $data['selectedGallery'];
			if(isset($data['page'])) $page = $data['page'];
		}

		// If gallery was selected from gallery dropdown.
		else if(Application::$app->request->isPost()){
			$data = Application::$app->request->post();
			if(array_key_exists('selectedGallery', $data)){
				$selectedGallery = $data['selectedGallery'];
			}
		}

		// Get images in selected gallery or all galleries and their count.
		if($selectedGallery > 0){
			$images = $imageModel->findAll(['gallery_id' => $selectedGallery], ['limit' => $pageSize, 'offset' => $page * $pageSize, 'orderBy' => ['created', 'desc']]);
			$total = $imageModel->count(['gallery_id' => $selectedGallery]);
		} else {
			$images = $imageModel->findAll([], ['limit' => $pageSize, 'offset' => $page * $pageSize, 'orderBy' => ['created', 'desc']]);
			$total = $imageModel->count();
		}

		// Get all galleries and format them to tree structure for the dropdown.
		$selectOptions[0] = ['text' => 'All galleries', 'options' => ['value' => 0]];
		if($selectedGallery == 0) $selectOptions[0]['options']['selected'] = 'selected';
		$allGalleries = $galleryModel->findAll([], ['orderBy' => ['name', 'asc']]);
		$selectOptions += $this->buildOptions($allGalleries, $selectedGallery, "&nbsp;&nbsp;&nbsp;&nbsp;");
		$galleryModel->selectedGallery = $selectedGallery;

		$this->view('imageadmin', [
			'galleryModel' => $galleryModel,
			'imageModel' => $imageModel,
			'selectOptions' => $selectOptions,
			'images' => $images,
			'page' => $page,
			'pageSize' => $pageSize,
			'total' => $total
		]);
	}

	/**
	 * User's image management.
	 */
	public function myimages(){
		// Check permission.
		if(!Auth::checkUserPermission('manageOwnImages')){
			Application::$app->request->redirect('users', 'login');
			exit;
		}

		$pageSize = 10;
		$page = 0;

		// If pagenav link was clicked.
		if(Application::$app->request->isGet()){
			$data = Application::$app->request->get();
			if(isset($data['selectedGallery'])) $selectedGallery = $data['selectedGallery'];
			if(isset($data['page'])) $page = $data['page'];
		}

		// Get all images of current user.
		$imageModel = new GalleryImage();
		$images = $imageModel->findAll(['user_id' => Application::$app->user->user_id], ['limit' => $pageSize, 'offset' => $page * $pageSize, 'orderBy' => ['name', 'asc']]);
		$total = $imageModel->count(['user_id' => Application::$app->user->user_id]);

		$this->view('myimages', [
			'imageModel' => $imageModel,
			'images' => $images,
			'page' => $page,
			'pageSize' => $pageSize,
			'total' => $total
		]);
	}

	/**
	 * Add new gallery.
	 */
	public function galleryadd(){
		// Check permission.
		if(!Auth::checkUserPermission('addGalleries')){
			Session::setFlash('error', 'You do not have permission to do that.');
			Application::$app->request->redirect('galleries', 'galleryadmin');
			exit;
		}

		$galleryModel = new Gallery();

		if(Application::$app->request->isPost()){
			$galleryModel->values(Application::$app->request->post());
			if($galleryModel->validate()){
				if($galleryModel->save()){
					Session::setFlash('success', 'Gallery has been created.');
					Application::$app->request->redirect('galleries', 'galleryadmin');
					exit;
				} else {
					Session::setFlash('error', 'Gallery could not be created.');
					Application::$app->request->redirect('galleries', 'galleryadmin');
					exit;
				}
			}
		}
		// Parent gallery selector's dropdown content.
		$allGalleries = $galleryModel->findAll();
		$selectOptions[0] = ['text' => 'None', 'options' => ['value' => '']];
		$selectOptions += $this->buildOptions($allGalleries, $galleryModel->parent_id, "&nbsp;&nbsp;&nbsp;&nbsp;");
		$this->view('galleryadd', ['model' => $galleryModel, 'selectOptions' => $selectOptions]);
	}

	/**
	 * Remove gallery.
	 */
	public function gallerydelete(){
		// Check permission.
		if(!Auth::checkUserPermission('deleteGalleries')){
			Session::setFlash('error', 'You do not have permission to do that.');
			Application::$app->request->redirect('galleries', 'galleryadmin');
			exit;
		}

		if(Application::$app->request->isGet()){
			$data = Application::$app->request->get();
			$this->recursiveDelete($data['gallery_id']);
			Session::setFlash('success', 'Gallery and its subgalleries with all of their images have been removed.');
			Application::$app->request->redirect('galleries', 'galleryadmin');
		}
	}

	/**
	 * Recursively deletes a gallery and all of its subgalleries.
	 */
	private function recursiveDelete($gallery_id){
		$galleryModel = new Gallery();
		$imageModel = new GalleryImage();

		// Find and loop through all subgalleries.
		$children = $galleryModel->findAll(['parent_id' => $gallery_id]);
		foreach($children as $child){
			$this->recursiveDelete($child['gallery_id']);
		}

		$gallery = $galleryModel->findOne(['gallery_id' => $gallery_id]);

		// Get the gallery's images.
		$images = $imageModel->findAll(['gallery_id' => $gallery_id]);
		foreach($images as $image){
			$this->deleteOneImage($image, $imageModel, $gallery);
		}

		// Delete gallery's directory and database entry.
		echo 'DELETING ' . $gallery['name'] . '<br>';
		if(is_dir(APPROOT . 'www/gallery/' . $gallery['filepath'])){
			rmdir(APPROOT . 'www/gallery/' . $gallery['filepath']);
		}
		$galleryModel->delete($gallery['gallery_id']);
	}

	/**
	 * Add image to gallery.
	 */
	public function imageadd(){
		// Check permission.
		if(!Auth::checkUserPermission('addImages')){
			Application::$app->request->redirect('users', 'login');
			exit;
		}

		$galleryModel = new Gallery();
		$imageModel = new GalleryImage();

		if(Application::$app->request->isPost()){
			$imageModel->values(Application::$app->request->post());
			// Create filename for uploaded image.
			$filename = $_FILES['imageFile']['name'];
			$extension = substr($filename, strrpos($filename, '.'));
			$uploadname = $imageModel->createName() . $extension;
			$imageModel->filename = $uploadname;
			if($imageModel->validate()){
				$gallery = $galleryModel->findOne($imageModel->gallery_id);
				// Move image into gallery.
				if(move_uploaded_file($_FILES['imageFile']['tmp_name'], APPROOT . 'www/gallery/' . $gallery['filepath'] . '/' . $uploadname)){
					if($imageModel->save()){
						Session::setFlashRender('success', 'Image has been uploaded.');
					} else {
						Session::setFlashRender('error', 'There was a problem uploading the image.');
						// Remove file.
						unlink(APPROOT . 'www/gallery/' . $gallery['filepath'] . '/' . $uploadname);
					}
				} else {
					Session::setFlashRender('error', 'There was a problem uploading the image.');
				}
			}
		}

		$allGalleries = $galleryModel->findAll([], ['orderBy' => ['name', 'asc']]);
		$selectOptions = $this->buildOptions($allGalleries, $galleryModel->selectedGallery, '&nbsp;&nbsp;&nbsp;&nbsp;');
		$this->view('imageadd', ['model' => $imageModel, 'selectOptions' => $selectOptions]);
	}

	/**
	 * Remove image from gallery.
	 */
	public function imagedelete(){
		// Multiple pages can send to delete action, redirect must send back to correct page.
		$sender = Application::$app->request->getReferer();
		$options = [];
		if(Application::$app->request->isGet()){
			$data = Application::$app->request->get();
			$imageModel = new GalleryImage();
			$image = $imageModel->findOne($data['image_id']);
			$imageModel->values($image);
			// Check permission: to delete all images, or to delet own images and current image is own.
			if(Auth::checkUserPermission('deleteAllImages') || Auth::checkUserPermission('deleteOwnImages', $imageModel)){
				$galleryModel = new Gallery();
				$gallery = $galleryModel->findOne($image['gallery_id']);
				// Remove database entry.
				if($this->deleteOneImage($image, $imageModel, $gallery)){
					Session::setFlash('success', 'Image has been removed.');
				} else {
					Session::setFlash('error' , 'Image could not be removed');
				}
			} else {
				Session::setFlash('error', 'You do not have permission to do that.');
			}
			// If link contained gallery selection data, forward it to redirected page.
			if(isset($data['selectedGallery'])) $options['selectedGallery'] = $data['selectedGallery'];
		}
		if($sender){
			Application::$app->request->redirect($sender['controller'], $sender['action'], $options);
		} else {
			Application::$app->request->redirect('galleries', 'myimages', $options);
		}
	}

	/**
	 * Deletes image with given id from database and removes its image and thumbnails from gallery directory.
	 */
	private function deleteOneImage($imageData, $imageModel, $galleryData){
		if($imageModel->delete($imageData['image_id'])){
			// Remove image and thumbnails from directory.
			$files = new DirectoryIterator('../www/gallery/' . $galleryData['filepath'] . '/');
			foreach($files as $file){
				if(stripos($file, $imageData['filename']) !== false){
					unlink('../www/gallery/' . $galleryData['filepath']. '/' . $file);
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Creates a select option list out of gallery data in tree order, indenting subgalleries.
	 *
	 * @param array $galleries Array of gallery entries from database.
	 * @param int $selected Gallery that should be set selected. Set to null to not set anything. Defaults to null.
	 * @param string $indent The string to indent gallery names with. Gets duplicated for each sub-gallery step.
	 * @param int $depth Depth counter. Default start value is zero.
	 * @param int|null $omit Gallery id to skip when constructing the tree, also skips all of its subgalleries. Defaults to null.
	 * @param int|null $parent Gallery id where tree build is started from, null means top. Defaults to null.
	 * @param array Returns array of galleries organized in tree form.
	 */
	private function buildOptions($galleries, $selected = null, $indent, $omit = null, $depth = 0, $parent = null){
		$options = [];
		// Loop through all galleries.
		foreach($galleries as $key => $gallery){
			if($gallery['gallery_id'] == $omit) continue;
			if($gallery['parent_id'] == $parent){
				// Remove processed galleries to make the loop shorter.
				unset($galleries[$key]);
				// Create content array.
				$content = [];
				$content['text'] = str_repeat($indent, $depth) . $gallery['name'];
				$content['options']['value'] = $gallery['gallery_id'];
				if($gallery['gallery_id'] == $selected){
					$content['options']['selected'] = 'selected';
				}
				$options[$gallery['gallery_id']] = $content;
				// Recurse this gallery's subgalleries to make them appear beneath it.
				$children = $this->buildOptions($galleries, $selected, $indent, $omit, $depth + 1, $gallery['gallery_id']);
				$options = $options + $children;
			}
		}
		return $options;
	}
}

?>