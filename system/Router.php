<?php

class Router {
	protected $routes = [];

	/**
	 * Adds new route.
	 *
	 * @param string $path The path of the route, normally in 'controller/action' format.
	 * @param string $controller Name of the controller this path will call.
	 * @param string $action Name of the action this path will call.
	 */
	public function addRoute($path, $controller, $action){
		$this->routes[$path] = ['controller' => $controller, 'action' => $action];
	}

	/**
	 * Looks for a route.
	 *
	 * @param string $path The route path to search.
	 * @return array|boolen Returns an array from the routes, or return false if nothing was found.
	 */
	public function getRoute($path){
		if(isset($this->routes[$path])){
			return $this->routes[$path];
		} else {
			return false;
		}
	}
}
?>