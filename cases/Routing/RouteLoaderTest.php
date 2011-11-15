<?php

class RouteLoaderTest extends PHPUnit_Framework_TestCase {

	public function __construct()
	{
		$this->loader = new Laravel\Routing\Loader(APP_PATH, ROUTE_PATH);
	}

	public function test_root_routes_can_be_loaded()
	{
		$routes = $this->loader->load('/');

		$expected = require APP_PATH.'routes'.EXT;

		$this->assertCount(count($expected), $routes);
		$this->assertArrayHasKey('GET /', $routes);
		$this->assertArrayHasKey('GET /root', $routes);
	}

	public function test_routes_in_route_folder_can_be_loaded_by_uri()
	{
		$routes = $this->loader->load('user/test');

		$root = require APP_PATH.'routes'.EXT;
		$user = require ROUTE_PATH.'user'.EXT;

		$this->assertCount(count($root + $user), $routes);
		$this->assertArrayHasKey('GET /user', $routes);
		$this->assertArrayHasKey('GET /user/login', $routes);

		$admin = require ROUTE_PATH.'admin'.EXT;

		$routes = $this->loader->load('admin');

		$this->assertCount(count($admin + $root), $routes);
		$this->assertArrayHasKey('GET /admin', $routes);
	}

	public function test_nested_routes_can_be_loaded_by_uri()
	{
		$routes = $this->loader->load('admin/panel/something');

		$panel = require ROUTE_PATH.'admin/panel'.EXT;
		$root = require APP_PATH.'routes'.EXT;

		$this->assertCount(count($root + $panel), $routes);
		$this->assertArrayHasKey('GET /', $routes);
		$this->assertArrayHasKey('GET /admin/panel', $routes);
	}

	public function test_route_loader_can_load_all_routes()
	{
		$root = require APP_PATH.'routes'.EXT;
		$user = require ROUTE_PATH.'user'.EXT;
		$admin = require ROUTE_PATH.'admin'.EXT;
		$panel = require ROUTE_PATH.'admin/panel'.EXT;

		$routes = $this->loader->everything();

		$this->assertCount(count($root + $user + $admin + $panel), $routes);
	}

}
