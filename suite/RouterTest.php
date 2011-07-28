<?php

class RoutingTest extends PHPUnit_Framework_TestCase {

	/**
	 * Mocked Router.
	 */
	public $router;

	public function setUp()
	{
		if (is_null($this->router))
		{
			$router = $this->getMockClass('System\\Router', array('load'));

			$routes = array();

			$routes['GET /'] = array('name' => 'root', 'do' => function() {return 'root';});
			$routes['GET /home'] = array('name' => 'home', 'do' => function() {});
			$routes['POST /home'] = array('name' => 'post-home', 'do' => function() {});
			$routes['GET /user/(:num)'] = array('name' => 'user', 'do' => function() {});
			$routes['GET /user/(:any)/(:num)/edit'] = array('name' => 'edit', 'do' => function() {});
			$routes['GET /cart/(:num?)'] = array('name' => 'cart', 'do' => function() {});
			$routes['GET /download/(:num?)/(:any?)'] = array('name' => 'download', 'do' => function() {});

			$router::staticExpects($this->any())->method('load')->will($this->returnValue($routes));

			$this->router = $router;
		}
	}

	public function tearDown()
	{
		Utils::rrmdir(APP_PATH.'routes');
	}

	public function testRouterReturnsNullWhenNotFound()
	{
		$router = $this->router;
		$this->assertNull($router::route('GET', 'doesnt-exist'));
	}

	public function testRouterRoutesToRootWhenItIsRequest()
	{
		$router = $this->router;
		$this->assertEquals($router::route('GET', '/')->callback['name'], 'root');
	}

	public function testRouterRoutesToProperRouteWhenSegmentsArePresent()
	{
		$router = $this->router;
		$this->assertEquals($router::route('GET', 'home')->callback['name'], 'home');
		$this->assertEquals($router::route('GET', 'user/1')->callback['name'], 'user');
		$this->assertEquals($router::route('GET', 'user/taylor/25/edit')->callback['name'], 'edit');
		$this->assertEquals($router::route('POST', 'home')->callback['name'], 'post-home');
	}

	public function testRouterGivesRouteProperSegmentsWhenTheyArePresent()
	{
		$router = $this->router;
		$this->assertEquals($router::route('GET', 'user/1')->parameters[0], 1);
		$this->assertEquals(count($router::route('GET', 'user/1')->parameters), 1);
		$this->assertEquals($router::route('GET', 'user/taylor/25/edit')->parameters[0], 'taylor');
		$this->assertEquals($router::route('GET', 'user/taylor/25/edit')->parameters[1], 25);
		$this->assertEquals(count($router::route('GET', 'user/taylor/25/edit')->parameters), 2);		
	}

	public function testRouterRoutesToProperRouteWhenUsingOptionalSegments()
	{
		$router = $this->router;
		$this->assertEquals($router::route('GET', 'cart')->callback['name'], 'cart');
		$this->assertEquals($router::route('GET', 'cart/1')->callback['name'], 'cart');
		$this->assertEquals($router::route('GET', 'download')->callback['name'], 'download');
		$this->assertEquals($router::route('GET', 'download/1')->callback['name'], 'download');
		$this->assertEquals($router::route('GET', 'download/1/a')->callback['name'], 'download');
	}

	public function testRouterGivesRouteProperOptionalSegmentsWhenTheyArePresent()
	{
		$router = $this->router;
		$this->assertTrue(is_array($router::route('GET', 'cart')->parameters));
		$this->assertEquals(count($router::route('GET', 'cart')->parameters), 0);
		$this->assertEquals($router::route('GET', 'cart/1')->parameters[0], 1);

		$this->assertEquals(count($router::route('GET', 'download')->parameters), 0);
		$this->assertEquals($router::route('GET', 'download/1')->parameters[0], 1);
		$this->assertEquals(count($router::route('GET', 'download/1')->parameters), 1);

		$this->assertEquals($router::route('GET', 'download/1/a')->parameters[0], 1);
		$this->assertEquals($router::route('GET', 'download/1/a')->parameters[1], 'a');
		$this->assertEquals(count($router::route('GET', 'download/1/a')->parameters), 2);
	}

	public function testRouterReturnsNullWhenRouteNotFound()
	{
		$router = $this->router;
		$this->assertNull($router::route('GET', 'user/taylor/taylor/edit'));
		$this->assertNull($router::route('GET', 'user/taylor'));
		$this->assertNull($router::route('GET', 'user/12-3'));
		$this->assertNull($router::route('GET', 'cart/a'));
		$this->assertNull($router::route('GET', 'cart/12-3'));
		$this->assertNull($router::route('GET', 'download/a'));
		$this->assertNull($router::route('GET', 'download/1a'));
		$this->assertNull($router::route('POST', 'user/taylor/25/edit'));
	}

	public function testRouteLoaderShouldReturnSingleRoutesFileWhenNoFolderIsPresent()
	{
		$router = $this->router;
		$this->assertArrayHasKey('GET /', $router::load('test'));
	}

	/**
	 * Note: It is OK for these tests to not use the mocked Router.
	 */
	public function testRouteLoaderLoadsRouteFilesInRouteDirectoryByURI()
	{
		$this->setupRoutesDirectory();

		$this->assertArrayHasKey('GET /user', System\Router::load('user'));
		$this->assertArrayHasKey('GET /cart/edit', System\Router::load('cart'));
		$this->assertArrayHasKey('GET /cart/edit', System\Router::load('cart/edit'));

		$this->setupNestedRouteFiles();

		// Retest the assertions above...
		$this->assertArrayHasKey('GET /user', System\Router::load('user'));
		$this->assertArrayHasKey('GET /cart/edit', System\Router::load('cart'));
		$this->assertArrayHasKey('GET /cart/edit', System\Router::load('cart/edit'));
		
		// Test the nested routes...
		$this->assertArrayHasKey('GET /user/edit', System\Router::load('user/edit'));
		$this->assertArrayHasKey('GET /user/edit', System\Router::load('user/edit/test'));
		$this->assertArrayHasKey('GET /admin/panel', System\Router::load('admin/panel'));
		$this->assertArrayHasKey('GET /user/update/admin', System\Router::load('user/update/admin'));
	}

	/**
	 * Note: It is OK for these tests to not use the mocked Router.
	 */
	public function testRouteLoaderLoadsBaseRoutesFileForEveryRequest()
	{
		$this->setupRoutesDirectory();
		$this->assertArrayHasKey('GET /', System\Router::load('user'));
	}

	public function testRouterCallMethodCallsRoutes()
	{
		$router = $this->router;
		$this->assertInstanceOf('System\\Response', $router::call('/'));
		$this->assertEquals($router::call('/')->content, 'root');
	}

	public function testRouterCallMethodReturnsNullWhenRouteDoesntExist()
	{
		$router = $this->router;
		$this->assertNull($router::call('doesnt.exist'));
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