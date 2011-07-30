<?php

class RoutingTest extends PHPUnit_Framework_TestCase {

	/**
	 * Dummy routes.
	 *
	 * @var array
	 */
	public $routes;

	public function setUp()
	{
		$routes = array();

		$routes['GET /'] = array('name' => 'root', 'do' => function() {return 'root';});
		$routes['GET /home'] = array('name' => 'home', 'do' => function() {});
		$routes['POST /home'] = array('name' => 'post-home', 'do' => function() {});
		$routes['GET /user/(:num)'] = array('name' => 'user', 'do' => function() {});
		$routes['GET /user/(:any)/(:num)/edit'] = array('name' => 'edit', 'do' => function() {});
		$routes['GET /cart/(:num?)'] = array('name' => 'cart', 'do' => function() {});
		$routes['GET /download/(:num?)/(:any?)'] = array('name' => 'download', 'do' => function() {});

		$this->routes = $routes;
	}

	public function tearDown()
	{
		Utils::rrmdir(APP_PATH.'routes');
	}

	public function testRouterReturnsNullWhenNotFound()
	{
		$this->assertNull(System\Router::make('GET', 'doesnt-exist')->route());
	}

	public function testRouterRoutesToRootWhenItIsRequest()
	{
		$router = new System\Router('GET', '/', $this->routes);
		$this->assertEquals($router->route()->callback['name'], 'root');
	}

	/**
	 * @dataProvider routeSegmentProvider
	 */
	public function testRouterRoutesToProperRouteWhenSegmentsArePresent($method, $uri, $name)
	{
		$router = new System\Router($method, $uri, $this->routes);
		$this->assertEquals($router->route()->callback['name'], $name);
	}

	public function routeSegmentProvider()
	{
		return array(
			array('GET', 'home', 'home'),
			array('GET', 'user/1', 'user'),
			array('GET', 'user/taylor/25/edit', 'edit'),
			array('POST', 'home', 'post-home'),
		);
	}

	public function testRouterGivesRouteProperSegmentsWhenTheyArePresent()
	{
		$this->assertEquals(System\Router::make('GET', 'user/1', $this->routes)->route()->parameters[0], 1);
		$this->assertEquals(count(System\Router::make('GET', 'user/1', $this->routes)->route()->parameters), 1);
		$this->assertEquals(System\Router::make('GET', 'user/taylor/25/edit', $this->routes)->route()->parameters[0], 'taylor');
		$this->assertEquals(System\Router::make('GET', 'user/taylor/25/edit', $this->routes)->route()->parameters[1], 25);
		$this->assertEquals(count(System\Router::make('GET', 'user/taylor/25/edit', $this->routes)->route()->parameters), 2);		
	}

	public function testRouterRoutesToProperRouteWhenUsingOptionalSegments()
	{
		$this->assertEquals(System\Router::make('GET', 'cart', $this->routes)->route()->callback['name'], 'cart');
		$this->assertEquals(System\Router::make('GET', 'cart/1', $this->routes)->route()->callback['name'], 'cart');
		$this->assertEquals(System\Router::make('GET', 'download', $this->routes)->route()->callback['name'], 'download');
		$this->assertEquals(System\Router::make('GET', 'download/1', $this->routes)->route()->callback['name'], 'download');
		$this->assertEquals(System\Router::make('GET', 'download/1/a', $this->routes)->route()->callback['name'], 'download');
	}

	public function testRouterGivesRouteProperOptionalSegmentsWhenTheyArePresent()
	{
		$this->assertTrue(is_array(System\Router::make('GET', 'cart', $this->routes)->route()->parameters));
		$this->assertEquals(count(System\Router::make('GET', 'cart', $this->routes)->route()->parameters), 0);
		$this->assertEquals(System\Router::make('GET', 'cart/1', $this->routes)->route()->parameters[0], 1);

		$this->assertEquals(count(System\Router::make('GET', 'download', $this->routes)->route()->parameters), 0);
		$this->assertEquals(System\Router::make('GET', 'download/1', $this->routes)->route()->parameters[0], 1);
		$this->assertEquals(count(System\Router::make('GET', 'download/1', $this->routes)->route()->parameters), 1);

		$this->assertEquals(System\Router::make('GET', 'download/1/a', $this->routes)->route()->parameters[0], 1);
		$this->assertEquals(System\Router::make('GET', 'download/1/a', $this->routes)->route()->parameters[1], 'a');
		$this->assertEquals(count(System\Router::make('GET', 'download/1/a', $this->routes)->route()->parameters), 2);
	}

	public function testRouterReturnsNullWhenRouteNotFound()
	{
		$this->assertNull(System\Router::make('GET', 'user/taylor/taylor/edit', $this->routes)->route());
		$this->assertNull(System\Router::make('GET', 'user/taylor', $this->routes)->route());
		$this->assertNull(System\Router::make('GET', 'user/12-3', $this->routes)->route());
		$this->assertNull(System\Router::make('GET', 'cart/a', $this->routes)->route());
		$this->assertNull(System\Router::make('GET', 'cart/12-3', $this->routes)->route());
		$this->assertNull(System\Router::make('GET', 'download/a', $this->routes)->route());
		$this->assertNull(System\Router::make('GET', 'download/1a', $this->routes)->route());
		$this->assertNull(System\Router::make('POST', 'user/taylor/25/edit', $this->routes)->route());
	}

	public function testRouteLoaderShouldReturnSingleRoutesFileWhenNoFolderIsPresent()
	{
		$router = new System\Router('GET', 'test');
		$this->assertArrayHasKey('GET /', $router->routes);
	}

	/**
	 * @dataProvider routeDirectoryRouteProvider
	 */
	public function testRouteLoaderLoadsRouteFilesInRouteDirectoryByURI($method, $uri, $key)
	{
		$this->setupRoutesDirectory();

		$router = new System\Router($method, $uri);
		$this->assertArrayHasKey($key, $router->routes);
	}

	/**
	 * @dataProvider routeDirectoryRouteProvider
	 * @dataProvider nestedRouteDirectoryRouteProvider
	 */
	public function testRouterLoaderLoadsRouteFilesInNestedRouteDirectoryByURI($method, $uri, $key)
	{
		$this->setupRoutesDirectory();
		$this->setupNestedRouteFiles();

		$router = new System\Router($method, $uri);
		$this->assertArrayHasKey($key, $router->routes);
	}

	public function routeDirectoryRouteProvider()
	{
		return array(
			array('GET', 'user', 'GET /user'),
			array('GET', 'cart', 'GET /cart/edit'),
			array('GET', 'cart/edit', 'GET /cart/edit'),
		);
	}

	public function nestedRouteDirectoryRouteProvider()
	{
		return array(
			array('GET', 'user/edit', 'GET /user/edit'),
			array('GET', 'user/edit', 'GET /user/edit/test'),
			array('GET', 'admin/panel', 'GET /admin/panel'),
			array('GET', 'user/update/admin', 'GET /user/update/admin'),
		);
	}

	/**
	 * Note: It is OK for these tests to not use the mocked Router.
	 */
	public function testRouteLoaderLoadsBaseRoutesFileForEveryRequest()
	{
		$this->setupRoutesDirectory();

		$router = new System\Router('GET', 'user');
		$this->assertArrayHasKey('GET /', $router->routes);
	}

	private function setupRoutesDirectory()
	{
		mkdir(APP_PATH.'routes', 0777);

		file_put_contents(APP_PATH.'routes/user.php', "<?php return array('GET /user' => function() {return '/user';}); ?>", LOCK_EX);
		file_put_contents(APP_PATH.'routes/cart.php', "<?php return array('GET /cart/edit' => function() {return '/cart/edit';}); ?>", LOCK_EX);
	}

	private function setupNestedRouteFiles()
	{
		mkdir(APP_PATH.'routes/admin', 0777);
		mkdir(APP_PATH.'routes/user', 0777);
		mkdir(APP_PATH.'routes/user/update', 0777);

		file_put_contents(APP_PATH.'routes/user/edit.php', "<?php return array('GET /user/edit' => function() {}, 'GET /user/edit/test' => function() {}); ?>", LOCK_EX);
		file_put_contents(APP_PATH.'routes/user/update/admin.php', "<?php return array('GET /user/update/admin' => function() {}); ?>", LOCK_EX);
		file_put_contents(APP_PATH.'routes/admin/panel.php', "<?php return array('GET /admin/panel' => function() {}); ?>", LOCK_EX);
	}

}