<?php

class RouteLoaderTest extends PHPUnit_Framework_TestCase {

	/**
	 * Tear down the test environment.
	 */
	public function tearDown()
	{
		Utils::remove_directory(APP_PATH.'routes');
	}

	/**
	 * The router should load the base routes file when no routes directory is present.
	 */
	public function testReturnRoutesFileWhenNoDirectory()
	{
		// Don't use the stub loader in this test since we just want to mimic the real Laravel base install.
		$loader = new System\Routing\Loader;
		$this->assertArrayHasKey('GET /', $loader->load('test'));
	}

	/**
	 * The route loader should load routes from a route directory that doesn't have nested routes.
	 *
	 * @dataProvider routeDirectoryRouteProvider
	 */
	public function testLoadsRouteFilesInRouteDirectoryByURI($uri, $key, $loader)
	{
		$this->setupRoutesDirectory();
		$this->assertArrayHasKey($key, $loader->load($uri));
	}

	public function routeDirectoryRouteProvider()
	{
		$loader = new System\Routing\Loader;

		return array(
			array('user', 'GET /user', $loader),
			array('cart', 'GET /cart/edit', $loader),
			array('cart/edit', 'GET /cart/edit', $loader),
		);
	}

	/**
	 * The route loader should load routes from a route directory that does have nested routes.
	 *
	 * @dataProvider nestedRouteDirectoryRouteProvider
	 */
	public function testLoadsRouteFilesInNestedRouteDirectoryByURI($uri, $key, $loader)
	{
		$this->setupRoutesDirectory();
		$this->setupNestedRouteFiles();
		$this->assertArrayHasKey($key, $loader->load($uri));
	}

	public function nestedRouteDirectoryRouteProvider()
	{
		$routes = $this->routeDirectoryRouteProvider();

		$loader = new System\Routing\Loader;

		$routes[] = array('user/edit', 'GET /user/edit', $loader);
		$routes[] = array('admin/panel', 'GET /admin/panel', $loader);
		$routes[] = array('user/update/admin', 'GET /user/update/admin', $loader);

		return $routes;
	}

	/**
	 * The route loader should load the base routes even when there is a route directory and
	 * routes from that route directory are being loaded.
	 */
	public function testLoadsBaseRoutesFileForEveryRequest()
	{
		$this->setupRoutesDirectory();

		$loader = new System\Routing\Loader;
		$this->assertArrayHasKey('GET /', $loader->load('user'));
	}

	/**
	 * The route loader should be able to load all routes using the everything method.
	 */
	public function testLoadsEverything()
	{
		$this->assertArrayHasKey('GET /', System\Routing\Loader::everything(true));

		$this->setupRoutesDirectory();
		$this->assertArrayHasKey('GET /', System\Routing\Loader::everything(true));
		$this->assertArrayHasKey('GET /user', System\Routing\Loader::everything(true));

		$this->setupNestedRouteFiles();
		$this->assertArrayHasKey('GET /', System\Routing\Loader::everything(true));
		$this->assertArrayHasKey('GET /user/edit', System\Routing\Loader::everything(true));
		$this->assertArrayHasKey('GET /user/update/admin', System\Routing\Loader::everything(true));
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