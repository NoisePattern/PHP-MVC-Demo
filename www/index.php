<?php

// DONE - Move everything from require.php to this file.
// DONE - Rewrite the core class to check for view method and file only if the controller has been found.
// DONE - Rewrite core class to use landing page and not found page defines set in config.
// DONE - Move __construct that creates database connection from various controller pages to root controller class and change it to read the model name from variable.
// DONE - Change controller methods' view call to only use view file name, the path should be set by root controller class' view method from variable.
// DONE - Merge header / navigation / footer files into single layout file and view method include it. the headed in turn should position the view file inlcude.
// DONE - Rearrange files: move app/libraries to another root-level directory and rewrite file path pointers to their files. Flatten the tree by moving all things under root.
// DONE - Create defines for paths so there's no need to move up and down the directory tree to find files.
// DONE - Rewrite model database connection use to share a single connection. Move common db methods from Database to Model.
// Move model validation rules to model files.

	// Require base app libraries and configuration.
	require_once('../config/config.php');
	require_once(SYSTEMROOT . 'Database.php');
	require_once(SYSTEMROOT . 'Session.php');
	require_once(SYSTEMROOT . 'Application.php');
	require_once(SYSTEMROOT . 'Auth.php');
	require_once(SYSTEMROOT . 'Controller.php');
	require_once(SYSTEMROOT . 'Model.php');
	require_once(SYSTEMROOT . 'Request.php');

	// Instantiate core.
	$app = new Application();
	$app->execute();
?>