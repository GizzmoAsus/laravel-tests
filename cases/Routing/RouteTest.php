<?php

use Laravel\Routing\Route;
use Laravel\Routing\Filter;

class RouteTest extends PHPUnit_Framework_TestCase {

	public function test_route_sets_proper_uris()
	{
		$route = new Route('GET /, GET /something, GET /something/else', function() {}, array());

		$this->assertTrue($route->handles('/'));
		$this->assertTrue($route->handles('something'));
		$this->assertTrue($route->handles('something/else'));
	}

	public function test_route_knows_its_own_name()
	{
		$route = new Route('GET /', array('name' => 'test', function() {}), array());

		$this->assertTrue($route->is('test'));
		$this->assertFalse($route->is('something'));
		$this->assertTrue($route->is_test());
		$this->assertFalse($route->is_something());
	}

	public function test_route_can_gets_the_filters_for_an_event()
	{
		$route = new Route('GET /', array('before' => 'auth|csrf|role:admin', 'after' => 'log', function() {}), array());

		$this->assertEquals(array('auth', 'csrf', 'role:admin'), $route->filters('before'));
		$this->assertEquals(array('log'), $route->filters('after'));
	}

	public function test_route_can_be_executed()
	{
		$route = new Route('GET /', function() {return 'GET /';});
		$this->assertEquals('GET /', $route->call()->content);

		$route = new Route('GET /', array(function() {return 'GET /';}));
		$this->assertEquals('GET /', $route->call()->content);
	}

	public function test_parameters_are_passed_to_route()
	{
		$route = new Route('GET /', function($name, $age) {return $name.'|'.$age;}, array('taylor', 25));
		$this->assertEquals('taylor|25', $route->call()->content);
	}

	public function test_before_filters_interrupt_route_response()
	{
		Filter::register(array('route_test_1' => function()
		{
			return 'Filtered!';
		}));

		$route = new Route('GET /', array('before' => 'route_test_1', function()
		{
			return 'GET /';
		}));

		$this->assertEquals('Filtered!', $route->call()->content);
	}

	public function test_before_filters_that_return_null_dont_interrupt_response()
	{
		Filter::register(array('route_test_2' => function()
		{
			return;
		}));

		$route = new Route('GET /', array('before' => 'route_test_2', function()
		{
			return 'GET /';
		}));

		$this->assertEquals('GET /', $route->call()->content);
	}

	public function test_after_filters_are_called_by_route()
	{
		Filter::register(array('route_test_3' => function()
		{
			define('ROUTE_AFTER_FILTER', 1);
		}));

		$route = new Route('GET /', array('after' => 'route_test_3', function()
		{
			return 'GET /';
		}));

		$this->assertEquals('GET /', $route->call()->content);
		$this->assertTrue(defined('ROUTE_AFTER_FILTER'));
	}

	public function test_routes_that_return_null_return_404_response()
	{
		$route = new Route('GET /', function() {return;}, array());
		$this->assertEquals(404, $route->call()->status);
	}

	public function test_controller_is_called_for_delegate_routes()
	{
		$route = new Route('GET /blog', 'blog@index', array());

		$this->assertEquals('blog@index', $route->call()->content);

		$route = new Route('GET /blog', array('after' => 'log', 'uses' => 'blog@index'));

		$this->assertEquals('blog@index', $route->call()->content);
	}

	public function test_parameters_are_passed_to_controller()
	{
		$route = new Route('GET /', 'blog@post', array(25));

		$this->assertEquals('post|25', $route->call()->content);
	}

	public function test_global_before_filter_gets_called()
	{
		$filters = require APP_PATH.'filters'.EXT;

		Filter::register(array('before' => function() {return 'Before!';}));

		$route = new Route('GET /', function() {return 'GET /';});

		$this->assertEquals('Before!', $route->call()->content);

		Filter::register(array('before' => $filters['before']));
	}

	public function test_global_after_filter_gets_called()
	{
		$filters = require APP_PATH.'filters'.EXT;

		Filter::register(array('after' => function() {define('ROUTE_GLOBAL_AFTER_FILTER', 2);}));

		$route = new Route('GET /', function() {return 'GET /';});

		$this->assertEquals('GET /', $route->call()->content);
		$this->assertTrue(defined('ROUTE_GLOBAL_AFTER_FILTER'));

		Filter::register(array('after' => $filters['after']));
	}

}
