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

		$this->assertTrue($route instanceof Route);
		$this->assertEquals($route->key, 'GET /');

		$route = $this->router->route('GET', 'root');

		$this->assertTrue($route instanceof Route);
		$this->assertEquals($route->key, 'GET /root');
	}

	public function test_router_can_route_to_nested_routes()
	{
		$route = $this->router->route('GET', 'user');

		$this->assertTrue($route instanceof Route);
		$this->assertEquals($route->key, 'GET /user');

		$route = $this->router->route('GET', 'user/login');

		$this->assertTrue($route instanceof Route);
		$this->assertEquals($route->key, 'GET /user/login');
	}

	public function test_router_can_route_to_deep_nested_route()
	{
		$route = $this->router->route('GET', 'admin/panel');

		$this->assertTrue($route instanceof Route);
		$this->assertEquals($route->key, 'GET /admin/panel');
	}

	public function test_router_sets_proper_parameters_on_route()
	{
		$route = $this->router->route('GET', 'user/profile/taylor');

		$this->assertTrue($route instanceof Route);
		$this->assertEquals($route->parameters, array('taylor'));
	}

	public function test_router_respects_wildcards()
	{
		$this->assertNull($this->router->route('GET', 'user/profile/@#)@)'));
		$this->assertTrue($this->router->route('GET', 'user/profile/taylor_otwell') instanceof Route);
		$this->assertNull($this->router->route('GET', 'user/id/taylor'));
		$this->assertTrue($this->router->route('GET', 'user/id/1') instanceof Route);
		$this->assertNull($this->router->route('GET', 'user/name/taylor/otwell'));
		$this->assertTrue($this->router->route('GET', 'user/name/taylor/1') instanceof Route);
		$this->assertTrue($this->router->route('GET', 'user/year/2011') instanceof Route);
		$this->assertNull($this->router->route('GET', 'user/year/taylor'));
	}

	public function test_router_sets_optional_wildcards()
	{
		$this->assertEquals($this->router->route('GET', 'user/year')->parameters, array());
		$this->assertEquals($this->router->route('GET', 'user/year/2020')->parameters, array(2020));
	}

	public function test_router_generates_appropriate_adhoc_route_for_controller()
	{
		$route = $this->router->route('GET', 'blog/something/taylor');

		$this->assertTrue($route instanceof Route);
		$this->assertEquals($route->key, 'GET /blog/something/taylor');
		$this->assertEquals($route->callback, 'blog@something');
		$this->assertEquals($route->parameters, array('taylor'));
	}

	public function test_router_returns_null_when_no_route_is_found()
	{
		$this->assertNull($this->router->route('POST', 'frodo'));
	}

}