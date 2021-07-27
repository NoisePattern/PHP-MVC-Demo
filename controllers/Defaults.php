<?php

class Defaults extends Controller {
	public $useModels = [];
	public $permissions = [];

	public function notfound(){
		$this->view('notfound');
	}

	public function error(){
		$this->view('error');
	}
}
?>