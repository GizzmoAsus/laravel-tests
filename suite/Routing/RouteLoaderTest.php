<?php

class RouteLoaderTest extends PHPUnit_Framework_TestCase {

	/**
	 * The path to the fixture routes.
	 *
	 * @var string
	 */
	public $route_path;

	public function setUp()
	{
		$this->route_path = FIXTURE_PATH.'routes/';
	}

	public function testReturnRoutesFileWhenNoDirectory()
	{
		$loader = new System\Routing\Loader(APP_PATH);
		$this->assertArrayHasKey('GET /', $loader->load('test'));
	}

	/**
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
			array('user', 'GET /user/root'),
			array('cart', 'GET /cart/edit'),
			array('cart/edit', 'GET /cart/edit'),
			array('user/root', 'GET /user/root'),
		);
	}

	/**
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

	public function testLoadsBaseRoutesFileForEveryRequest()
	{
		$loader = new System\Routing\Loader($this->route_path);
		$this->assertArrayHasKey('GET /', $loader->load('user'));
	}

	public function testLoaderCanLoadEverything()
	{
		System\Config::set('application.modules', array('auth'));
		$this->assertArrayHasKey('GET /', System\Routing\Loader::all(true, $this->route_path));
		$this->assertArrayHasKey('GET /auth', System\Routing\Loader::all(true, $this->route_path));
		$this->assertArrayHasKey('GET /user', System\Routing\Loader::all(true, $this->route_path));
		$this->assertArrayHasKey('GET /user/update/admin', System\Routing\Loader::all(true, $this->route_path));
		Config::set('application.modules', array());
	}

}