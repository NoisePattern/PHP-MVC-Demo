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
	require_once(SYSTEMROOT . 'Router.php');

	// Instantiate core.
	$app = new Application();

	// Add routes. Done here because site-specific stuff should be kept out of system files.
	$app->router->addRoute('/', 'Articles', 'index');
	$app->router->addRoute('/articles/admin', 'Articles', 'admin');
	$app->router->addRoute('/articles/index', 'Articles', 'index');
	$app->router->addRoute('/articles/article', 'Articles', 'article');
	$app->router->addRoute('/articles/myarticles', 'Articles', 'myarticles');
	$app->router->addRoute('/articles/write', 'Articles', 'write');
	$app->router->addRoute('/articles/edit', 'Articles', 'edit');
	$app->router->addRoute('/articles/delete', 'Articles', 'delete');
	$app->router->addRoute('/users/login', 'Users', 'login');
	$app->router->addRoute('/users/logout', 'Users', 'logout');
	$app->router->addRoute('/users/register', 'Users', 'register');
	$app->router->addRoute('/galleries/index', 'Galleries', 'index');
	$app->router->addRoute('/galleries/galleryadmin', 'Galleries', 'galleryadmin');
	$app->router->addRoute('/galleries/galleryadd', 'Galleries', 'galleryadd');
	$app->router->addRoute('/galleries/gallerydelete', 'Galleries', 'gallerydelete');
	$app->router->addRoute('/galleries/imageadmin', 'Galleries', 'imageadmin');
	$app->router->addRoute('/galleries/myimages', 'Galleries', 'myimages');
	$app->router->addRoute('/galleries/imageadd', 'Galleries', 'imageadd');
	$app->router->addRoute('/galleries/imagedelete', 'Galleries', 'imagedelete');

	$app->execute();
?>