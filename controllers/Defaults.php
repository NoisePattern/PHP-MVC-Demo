<?php

class Defaults extends Controller {

	public function notfound(){
		$this->view('notfound');
	}

	public function error(){
		$this->view('error');
	}
}

?>