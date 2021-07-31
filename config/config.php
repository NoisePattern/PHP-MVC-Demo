<?php

	// Database connection parameters.
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'mvc_test');
	define('DB_USERNAME', 'mvc_test');
	define('DB_PASSWORD', 'OkZCbv8fiMwYfEjU');

	// URL root path.
	define('URLROOT', 'http://localhost/mvc_test');

	// Directory separator.
	define('DS', DIRECTORY_SEPARATOR);

	// App root path.
	define('APPROOT', dirname(dirname(__FILE__)) . DS);

	// System path.
	define('SYSTEMROOT', APPROOT . 'system' . DS);

	// Default layout file for views.
	define('DEFAULT_LAYOUT', 'default_layout');

	// Landing page path. If URL does not define controller and method, landing page is set as target.
	define('LANDING_CONTROLLER', 'articles');
	define('LANDING_METHOD', 'index');

	// Page not found path. If controller and/or method in URL are not found, not found page is set as target.
	define('NOTFOUND_CONTROLLER', 'Defaults');
	define('NOTFOUND_METHOD', 'notfound');

	// Website name.
	define('SITETITLE', 'Title');

	// Base permission policy. If set to false, actions that do not have permission check set up can be viewed.
	// If set to true, actions that do not have permission check set up cannot be viewed.
	define('PERMISSION_STRICT', false);
?>