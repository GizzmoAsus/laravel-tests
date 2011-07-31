<?php

class RouteFinderTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		$routes = array();

		$routes['GET /home'] = array('GET /home' => array('name' => 'home', 'do' => function() {}));
		$routes['GET /user'] = array('GET /user' => array('name' => 'user', 'do' => function() {}));

		System\Route_Finder::$routes = $routes;
	}

	public function tearDown()
	{
		Utils::rrmdir(APP_PATH.'routes');
	}

	public function testRouteFinderReturnsNullWhenRouteIsNotFound()
	{
		$this->assertNull(System\Route_Finder::find('doesnt-exist'));
	}

	public function testRouteFinderReturnsRouteWhenFoundInSingleRoutesFile()
	{
		$this->assertArrayHasKey('GET /home', System\Route_Finder::find('home'));
		$this->assertArrayHasKey('GET /user', System\Route_Finder::find('user'));
	}

	public function testRouteFinderLoadsRoutesFromRouteDirectoryToFindRoutes()
	{
		System\Route_Finder::$routes = null;
		$this->setupRoutesDirectory();

		$this->assertArrayHasKey('GET /user', System\Route_Finder::find('user'));
	}

	public function testRouteFinderLoadsBaseRoutesWhenFindingRoutesWithRouteFolder()
	{
		System\Route_Finder::$routes = null;
		$this->setupRoutesDirectory();

		System\Route_Finder::find('user');
		$this->assertArrayHasKey('GET /', System\Route_Finder::$routes);
		$this->assertArrayHasKey('GET /user/admin', System\Route_Finder::$routes);
	}

	private function setupRoutesDirectory()
	{
		mkdir(APP_PATH.'routes', 0777);
		mkdir(APP_PATH.'routes/user', 0777);

		file_put_contents(APP_PATH.'routes/user.php', "<?php return array('GET /user' => array('name' => 'user', 'do' => function() {})); ?>", LOCK_EX);		
		file_put_contents(APP_PATH.'routes/user/admin.php', "<?php return array('GET /user/admin' => array('name' => 'admin', 'do' => function() {})); ?>", LOCK_EX);		
	}

}