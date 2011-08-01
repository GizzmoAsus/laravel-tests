<?php

class RouteFinderTest extends PHPUnit_Framework_TestCase {

	/**
	 * The test routes.
	 *
	 * @var array
	 */
	private $routes;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		$routes = array();

		$routes['GET /home'] = array('GET /home' => array('name' => 'home', 'do' => function() {}));
		$routes['GET /user'] = array('GET /user' => array('name' => 'user', 'do' => function() {}));

		$this->routes = $routes;
	}

	/**
	 * The route finder should return null when no matching named route is found.
	 */
	public function testReturnsNullWhenRouteIsNotFound()
	{
		$this->assertNull(System\Routing\Finder::find('doesnt-exist', $this->routes));
	}

	/**
	 * The route finder should return the route when a matching named route is found.
	 */
	public function testReturnsRouteWhenFound()
	{
		$this->assertArrayHasKey('GET /home', System\Routing\Finder::find('home', $this->routes));
		$this->assertArrayHasKey('GET /user', System\Routing\Finder::find('user', $this->routes));
	}

}