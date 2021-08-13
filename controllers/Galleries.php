<?php

class Galleries extends Controller {
	/**
	 * Define an array of models that are required.
	 */
	public function useModels(){
		return [
			'Gallery',
			'GalleryImages'
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
			]
		];
	}

	/**
	 * View gallery.
	 */
	public function index(){

	}

	/**
	 * Manage galleries.
	 */
	public function galleryadmin(){
		$selectedGallery = null;					// Gallery selection dropdown's current selection.
		$selectOptions = [];						// List of galleries in gallery selection dropdown.
		$parentSelectOptions = [];					// List of valid parents for gallery in gallery settings.
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

		$allGalleries = $galleryModel->findAll();
		// Get all galleries and format them to tree structure.
		$selectOptions = $this->buildTreeFlat($allGalleries, "&nbsp;&nbsp;&nbsp;&nbsp;", true);
		// Generate parent gallery changer select's content, omit the current gallery from choices.
		$parentSelectOptions = $this->buildTreeFlat($allGalleries, "&nbsp;&nbsp;&nbsp;&nbsp;", true, $selectedGallery);
		// Set selection to gallery select dropdown.
		$galleryModel->selectedGallery = $selectedGallery;
		$this->view(
			'galleryadmin',
			[
				'galleryModel' => $galleryModel,
				'selectOptions' => $selectOptions,
				'selectedGallery' => $selectedGallery,
				'parentSelectOptions' => $parentSelectOptions
			]
		);
	}

	/**
	 * Manage images in gallery.
	 */
	public function imageadmin(){

	}

	/**
	 * Add new gallery.
	 */
	public function galleryadd(){
		$galleryModel = new Gallery();
		$allGalleries = $galleryModel->findAll();
		$selectOptions = $this->buildTreeFlat($allGalleries, "&nbsp;&nbsp;&nbsp;&nbsp;", true);
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
		$this->view('galleryadd', ['model' => $galleryModel, 'selectOptions' => $selectOptions]);
	}

	/**
	 * Remove gallery.
	 */
	public function gallerydelete(){
		$galleryModel = new Gallery();
		$data = Application::$app->request->get();
		$gallery = $galleryModel->findOne(['gallery_id' => $data['gallery_id']]);
		// Delete files gallery.
		// Implement once image management is written.

		// Delete directory and database entry.
		rmdir(APPROOT . 'www/galleries/' . $gallery['filepath']);
		$galleryModel->delete($gallery['gallery_id']);
		Session::setFlash('success', 'Gallery has been removed.' . APPROOT . 'www/galleries/' . $gallery['filepath']);
		Application::$app->request->redirect('galleries', 'galleryadmin');
	}

	/**
	 * Add image to gallery.
	 */
	public function imageadd(){

	}

	/**
	 * Remove image(s) from gallery.
	 */
	public function imageremove(){

	}

	/**
	 * Creates a tree structure out of gallery data where subgalleries are set to child array.
	 *
	 * @param array $galleries Array of gallery entries from database.
	 * @param int|null $parent Gallery id where tree build is started from, null means top. Defaults to null.
	 * @return array Returns array of galleries organized in tree form.
	 */
	private function buildTree($galleries, $parent = NULL){
		$branch = [];
		foreach($galleries as $key => $gallery){
			unset($galleries[$key]);
			if($gallery['parent_id'] == $parent){
				$children = $this->buildTree($galleries, $gallery['gallery_id']);
				$gallery['children'] = $children;
				$branch[] = $gallery;
			}
		}
		return $branch;
	}

	/**
	 * Creates a tree structure out of gallery data where all galleries are at same array depth but subgallery names are indented.
	 *
	 * @param array $galleries Array of gallery entries from database.
	 * @param string $indent The string to indent gallery names with. Gets duplicated for each sub-gallery step.
	 * @param bool $forSelect If set true, result is formatted into a key-value array for use in select elements. Otherwise all gallery data is set into the array. Default is false.
	 * @param int $depth Depth counter. Default start value is zero.
	 * @param int|null $omit Gallery id to skip when constructing the tree. Defaults to null.
	 * @param int|null $parent Gallery id where tree build is started from, null means top. Defaults to null.
	 * @param array Returns array of galleries organized in tree form.
	 */
	private function buildTreeFlat($galleries, $indent, $forSelect = false, $omit = null, $depth = 0, $parent = NULL){
		$branch = [];
		foreach($galleries as $key => $gallery){
			unset($galleries[$key]);
			if($gallery['gallery_id'] == $omit) continue;
			if($gallery['parent_id'] == $parent){
				$gallery['name'] = str_repeat($indent, $depth) . $gallery['name'];
				if($forSelect){
					$branch[$gallery['gallery_id']] = $gallery['name'];
				} else {
					$branch[] = $gallery;
				}
				$children = $this->buildTreeFlat($galleries, $indent, $forSelect, $omit, $depth + 1, $gallery['gallery_id']);
				$branch = $branch + $children;
			}
		}
		return $branch;
	}
}

?>