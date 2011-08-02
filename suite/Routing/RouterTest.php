<?php

class RouterTest extends PHPUnit_Framework_TestCase {

	/**
	 * The stubbed route loader.
	 *
	 * @var Loader
	 */
	public $loader;

	/**
	 * Setup the test environment.
	 */
	public function setUp()
	{
		// Create an array of variously defined routes to test with.
		$routes = array(
			'GET /'                             => array('name' => 'root', 'do' => function() {return 'root';}),
			'GET /home'                         => array('name' => 'home', 'do' => function() {}),
			'POST /home'                        => array('name' => 'post-home', 'do' => function() {}),
			'GET /user/(:num)'                  => array('name' => 'user', 'do' => function() {}),
			'GET /cart/(:num?)'                 => array('name' => 'cart', 'do' => function() {}),
			'GET /download/(:num?)/(:any?)'     => array('name' => 'download', 'do' => function() {}),
			'GET /user/(:any)/(:num)/edit'      => array('name' => 'edit', 'do' => function() {}),
		);

		// Create a stub of the Route Loader.
		$this->loader = $this->getMock('System\\Routing\\Loader', array(), array(APP_PATH));
		$this->loader->expects($this->any())->method('load')->will($this->returnValue($routes));
	}

	/**
	 * The router should return null when no matching route is found.
	 */
	public function testReturnsNullWhenNotFound()
	{
		$this->loader->expects($this->any())->method('load')->will($this->returnValue(array()));
		$this->assertNull(System\Routing\Router::make('GET', 'test', $this->loader)->route());
	}

	/**
	 * The router should be able to handle a request to root.
	 */
	public function testRoutesToRoot()
	{
		$this->assertEquals(System\Routing\Router::make('GET', '/', $this->loader)->route()->callback['name'], 'root');
	}

	/**
	 * The router should be able to route to the proper routes when segments are present.
	 *
	 * @dataProvider segmentedRouteProvider
	 */
	public function testRoutesWhenSegmentsArePresent($method, $uri, $name)
	{
		$this->assertEquals(System\Routing\Router::make($method, $uri, $this->loader)->route()->callback['name'], $name);
	}

	public function segmentedRouteProvider()
	{
		return array(
			array('GET', 'home', 'home'),
			array('GET', 'user/1', 'user'),
			array('POST', 'home', 'post-home'),
			array('GET', 'user/taylor/25/edit', 'edit'),
		);
	}

	/**
	 * The router should be able to parse segments into parameters to give to the route.
	 */
	public function testParsesSegmentsIntoParameters()
	{
		// Test with a single parameter.
		$this->assertEquals(System\Routing\Router::make('GET', 'user/1', $this->loader)->route()->parameters[0], 1);
		$this->assertEquals(count(System\Routing\Router::make('GET', 'user/1', $this->loader)->route()->parameters), 1);

		// Test with two parameters.
		$this->assertEquals(System\Routing\Router::make('GET', 'user/taylor/25/edit', $this->loader)->route()->parameters[1], 25);
		$this->assertEquals(System\Routing\Router::make('GET', 'user/taylor/25/edit', $this->loader)->route()->parameters[0], 'taylor');
		$this->assertEquals(count(System\Routing\Router::make('GET', 'user/taylor/25/edit', $this->loader)->route()->parameters), 2);

		// Test with optional parameters (both one and two).
		$this->assertEquals(System\Routing\Router::make('GET', 'cart/1', $this->loader)->route()->parameters[0], 1);
		$this->assertEquals(count(System\Routing\Router::make('GET', 'cart/1', $this->loader)->route()->parameters), 1);

		$this->assertEquals(System\Routing\Router::make('GET', 'download/1', $this->loader)->route()->parameters[0], 1);
		$this->assertEquals(count(System\Routing\Router::make('GET', 'download/1', $this->loader)->route()->parameters), 1);

		$this->assertEquals(System\Routing\Router::make('GET', 'download/1/a', $this->loader)->route()->parameters[0], 1);
		$this->assertEquals(System\Routing\Router::make('GET', 'download/1/a', $this->loader)->route()->parameters[1], 'a');
		$this->assertEquals(count(System\Routing\Router::make('GET', 'download/1/a', $this->loader)->route()->parameters), 2);
	}

	/**
	 * The router should correctly route when optional segments are present.
	 */
	public function testRoutesWhenUsingOptionalSegments()
	{
		$this->assertEquals(System\Routing\Router::make('GET', 'cart', $this->loader)->route()->callback['name'], 'cart');
		$this->assertEquals(System\Routing\Router::make('GET', 'cart/1', $this->loader)->route()->callback['name'], 'cart');
		$this->assertEquals(System\Routing\Router::make('GET', 'download', $this->loader)->route()->callback['name'], 'download');
		$this->assertEquals(System\Routing\Router::make('GET', 'download/1', $this->loader)->route()->callback['name'], 'download');
		$this->assertEquals(System\Routing\Router::make('GET', 'download/1/a', $this->loader)->route()->callback['name'], 'download');

		// The router should return null if the optional route parameters do not match the request URI.
		$this->assertNull(System\Routing\Router::make('GET', 'cart/a', $this->loader)->route());
		$this->assertNull(System\Routing\Router::make('GET', 'cart/1/a', $this->loader)->route());
		$this->assertNull(System\Routing\Router::make('GET', 'download/a/1', $this->loader)->route());
		$this->assertNull(System\Routing\Router::make('GET', 'download/1/a/c', $this->loader)->route());
	}

}