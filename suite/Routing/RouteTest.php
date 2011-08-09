<?php

class RouteTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		System\Routing\Filter::clear();
	}

	public function testRouteCallbackReturnsResponseInstance()
	{
		$route = new System\Routing\Route('GET /', function() {return 'test';});
		$this->assertEquals($route->call()->content, 'test');
		$this->assertInstanceOf('System\\Response', $route->call());
	}

	public function testRouteCallPassesParametersToCallback()
	{
		$route = new System\Routing\Route('GET /', function($parameter) {return $parameter;}, array('test'));
		$this->assertEquals($route->call()->content, 'test');

		$route = new System\Routing\Route('GET /', function($parameter1, $parameter2) {return $parameter1.' '.$parameter2;}, array('test1', 'test2'));
		$this->assertEquals($route->call()->content, 'test1 test2');
	}

	public function testNullBeforeFilterReturnsRouteResponse()
	{
		System\Routing\Filter::register(array('test' => function() {return null;}));
		$route = new System\Routing\Route('GET /', array('before' => 'test', 'do' => function() {return 'route';}));
		$this->assertEquals($route->call()->content, 'route');
	}

	public function testOverridingBeforeFilterReturnsFilterResponse()
	{
		System\Routing\Filter::register(array('test' => function() {return 'filter';}));
		$route = new System\Routing\Route('GET /', array('before' => 'test', 'do' => function() {return 'route';}));
		$this->assertEquals($route->call()->content, 'filter');
	}

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