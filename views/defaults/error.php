<?php

	if(isset($_SESSION['errorPage']['topic'])){
		echo '<h1>' . $_SESSION['errorPage']['topic'] . '</h1>';
	}
	if(isset($_SESSION['errorPage']['message'])){
		echo '<p>' . $_SESSION['errorPage']['message'] . '</p>';
	}

	unsetErrorPage();

?>