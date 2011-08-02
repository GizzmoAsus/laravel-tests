<?php

class RouteLoaderTest extends PHPUnit_Framework_TestCase {

	/**
	 * The path to the fixture routes.
	 */
	public $route_path;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$this->route_path = FIXTURE_PATH.'routes/';
	}

	/**
	 * The router should load the base routes file when no routes directory is present.
	 */
	public function testReturnRoutesFileWhenNoDirectory()
	{
		$loader = new System\Routing\Loader(APP_PATH);
		$this->assertArrayHasKey('GET /', $loader->load('test'));
	}

	/**
	 * The route loader should load routes from a route directory that doesn't have nested routes.
	 *
	 * @dataProvider routeDirectoryRouteProvider
	 */
	public function testLoadsRouteFilesInRouteDirectoryByURI($uri, $key)
	{
		$loader = new System\Routing\Loader($this->route_path);
		$this->assertArrayHasKey($key, $loader->load($uri));
	}

	public function routeDirectoryRouteProvider()
	{
		return array(
			array('user', 'GET /user'),
			array('cart', 'GET /cart/edit'),
			array('cart/edit', 'GET /cart/edit'),
		);
	}

	/**
	 * The route loader should load routes from a route directory that does have nested routes.
	 *
	 * @dataProvider nestedRouteDirectoryRouteProvider
	 */
	public function testLoadsRouteFilesInNestedRouteDirectoryByURI($uri, $key)
	{
		$loader = new System\Routing\Loader($this->route_path);
		$this->assertArrayHasKey($key, $loader->load($uri));
	}

	public function nestedRouteDirectoryRouteProvider()
	{
		$routes = $this->routeDirectoryRouteProvider();

		$routes[] = array('user/edit', 'GET /user/edit');
		$routes[] = array('admin/panel', 'GET /admin/panel');
		$routes[] = array('user/update/admin', 'GET /user/update/admin');

		return $routes;
	}

	/**
	 * The route loader should load the base routes even when there is a route directory and
	 * routes from that route directory are being loaded.
	 */
	public function testLoadsBaseRoutesFileForEveryRequest()
	{
		$loader = new System\Routing\Loader($this->route_path);
		$this->assertArrayHasKey('GET /', $loader->load('user'));
	}

	/**
	 * The route loader should be able to load all routes using the everything method.
	 */
	public function testLoadsEverything()
	{
		$this->assertArrayHasKey('GET /', System\Routing\Loader::all(true, $this->route_path));
		$this->assertArrayHasKey('GET /user', System\Routing\Loader::all(true, $this->route_path));
		$this->assertArrayHasKey('GET /user/update/admin', System\Routing\Loader::all(true, $this->route_path));
		
	}

}