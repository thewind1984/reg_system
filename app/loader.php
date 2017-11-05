<?php

	ini_set("display_errors", "on");
	error_reporting(E_ALL);

	require_once APP_DIR . '/autoloader.php';
	
	$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$path = trim($path, '/');
	$path = $path ? explode('/', $path) : null;
	
	//$controller = $path ? (sizeOf($path) > 1 ? array_slice($path, 0, -1) : $path) : array('index');
	$controller = $path && sizeOf($path) ? [$path[0]] : ['index'];
	$action = $path && sizeOf($path) > 1 ? $path[1] : 'index';
	$input = $path && sizeOf($path) > 2 ? array_slice($path, 2) : [];
	
	$controller = '\\Controller\\' . implode('\\', array_map(function($v){ return ucfirst(strtolower($v)); }, $controller));
	
	try {
		$class = new $controller($controller, $action, $input);
	} catch (Exception $e) {
		die('<b>' . $e->getMessage() . '</b>');
	}
	
	$class->run();

?>