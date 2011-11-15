<?php

use Laravel\Routing\Route;
use Laravel\Routing\Router;
use Laravel\Routing\Loader;

class RouterTest extends PHPUnit_Framework_TestCase {

	public function __construct()
	{
		$this->router = new Router(new Loader(APP_PATH, ROUTE_PATH), CONTROLLER_PATH);
	}

	public function test_router_can_find_named_route()
	{
		$route = $this->router->find('login');

		$this->assertArrayHasKey('GET /user/login', $route);
		$this->assertNull($this->router->find('doesnt-exist'));
	}

	public function test_router_can_route_to_root_routes()
	{
		$route = $this->router->route('GET', '/');

		$this->assertInstanceOf('Laravel\Routing\Route', $route);
		$this->assertEquals('GET /', $route->key);

		$route = $this->router->route('GET', 'root');

		$this->assertInstanceOf('Laravel\Routing\Route', $route);
		$this->assertEquals('GET /root', $route->key);
	}

	public function test_router_can_route_to_nested_routes()
	{
		$route = $this->router->route('GET', 'user');

		$this->assertInstanceOf('Laravel\Routing\Route', $route);
		$this->assertEquals('GET /user', $route->key);

		$route = $this->router->route('GET', 'user/login');

		$this->assertInstanceOf('Laravel\Routing\Route', $route);
		$this->assertEquals('GET /user/login', $route->key);
	}

	public function test_router_can_route_to_deep_nested_route()
	{
		$route = $this->router->route('GET', 'admin/panel');

		$this->assertInstanceOf('Laravel\Routing\Route', $route);
		$this->assertEquals('GET /admin/panel', $route->key);
	}

	public function test_router_sets_proper_parameters_on_route()
	{
		$route = $this->router->route('GET', 'user/profile/taylor');

		$this->assertInstanceOf('Laravel\Routing\Route', $route);
		$this->assertEquals(array('taylor'), $route->parameters);
	}

	public function test_router_respects_wildcards()
	{
		$this->assertNull($this->router->route('GET', 'user/profile/@#)@)'));
		$this->assertInstanceOf('Laravel\Routing\Route', $this->router->route('GET', 'user/profile/taylor_otwell'));
		$this->assertNull($this->router->route('GET', 'user/id/taylor'));
		$this->assertInstanceOf('Laravel\Routing\Route', $this->router->route('GET', 'user/id/1'));
		$this->assertNull($this->router->route('GET', 'user/name/taylor/otwell'));
		$this->assertInstanceOf('Laravel\Routing\Route', $this->router->route('GET', 'user/name/taylor/1'));
		$this->assertInstanceOf('Laravel\Routing\Route', $this->router->route('GET', 'user/year/2011'));
		$this->assertNull($this->router->route('GET', 'user/year/taylor'));
	}

	public function test_router_sets_optional_wildcards()
	{
		$this->assertEquals(array(), $this->router->route('GET', 'user/year')->parameters);
		$this->assertEquals(array(2020), $this->router->route('GET', 'user/year/2020')->parameters);
	}

	public function test_router_generates_appropriate_adhoc_route_for_controller()
	{
		$route = $this->router->route('GET', 'blog/something/taylor');

		$this->assertInstanceOf('Laravel\Routing\Route', $route);
		$this->assertEquals('GET /blog/something/taylor', $route->key);
		$this->assertEquals('blog@something', $route->callback);
		$this->assertEquals(array('taylor'), $route->parameters);
	}

	public function test_router_returns_null_when_no_route_is_found()
	{
		$this->assertNull($this->router->route('POST', 'frodo'));
	}

}
