<?php

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