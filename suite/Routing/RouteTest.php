<?php

class RouteTest extends PHPUnit_Framework_TestCase {

	/**
	 * Tear down the test environment.
	 */
	public function tearDown()
	{
		System\Routing\Filter::clear();
	}

	/**
	 * Calling a route should always return a Response instance.
	 */
	public function testRouteCallbackReturnsResponseInstance()
	{
		$route = new System\Routing\Route('GET /', function() {return 'test';});
		$this->assertEquals($route->call()->content, 'test');
		$this->assertInstanceOf('System\\Response', $route->call());
	}

	/**
	 * Calling a route should result in the route parameters being passed to the route callback.
	 */
	public function testRouteCallPassesParametersToCallback()
	{
		$route = new System\Routing\Route('GET /', function($parameter) {return $parameter;}, array('test'));
		$this->assertEquals($route->call()->content, 'test');

		$route = new System\Routing\Route('GET /', function($parameter1, $parameter2) {return $parameter1.' '.$parameter2;}, array('test1', 'test2'));
		$this->assertEquals($route->call()->content, 'test1 test2');
	}

	/**
	 * Calling a route with a null before filter should return the route response.
	 */
	public function testNullBeforeFilterReturnsRouteResponse()
	{
		System\Routing\Filter::register(array('test' => function() {return null;}));
		$route = new System\Routing\Route('GET /', array('before' => 'test', 'do' => function() {return 'route';}));
		$this->assertEquals($route->call()->content, 'route');
	}

	/**
	 * Calling a route with a before filter that returns a response should return the filter response.
	 */
	public function testOverridingBeforeFilterReturnsFilterResponse()
	{
		System\Routing\Filter::register(array('test' => function() {return 'filter';}));
		$route = new System\Routing\Route('GET /', array('before' => 'test', 'do' => function() {return 'route';}));
		$this->assertEquals($route->call()->content, 'filter');
	}

	/**
	 * Calling a route with an after filter should return the route response, even if the filter returns a response.
	 */
	public function testRouteAfterFilterIsCalled()
	{
		$route = new System\Routing\Route('GET /', array('after' => 'test', 'do' => function() {return 'route';}));
		System\Routing\Filter::register(array('test' => function() {define('LARAVEL_TEST_AFTER_FILTER', 'ran');}));
		$route->call();
		$this->assertTrue(defined('LARAVEL_TEST_AFTER_FILTER'));
	}

	public function testRouteAfterFilterDoesNotAffectResponse()
	{
		$route = new System\Routing\Route('GET /', array('after' => 'test', 'do' => function() {return 'route';}));
		System\Routing\Filter::register(array('test' => function() {return 'filter';}));
		$this->assertEquals($route->call()->content, 'route');
	}

}