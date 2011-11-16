<?php namespace Laravel; use PHPUnit_Framework_TestCase;

use Laravel\Routing\Route;
use Laravel\Routing\Router;
use Laravel\Routing\Loader;

/*
 * Test the Route related methods of the Rediect class
 */
class RedirectToRouteTest extends PHPUnit_Framework_TestCase {

	static function setUpBeforeClass()
	{
		$router = new Router(new Loader(APP_PATH, ROUTE_PATH), CONTROLLER_PATH);
		IoC::instance('laravel.routing.router', $router);
	}

	function testRedirectToNamedRoute()
	{
		$url = URL::to_login();
		$response = Redirect::to_login();
		$this->assertEquals($url, $response->headers['Location']);
	}

	function testRedirectToSecureNamedRoute()
	{
		$url = URL::to_secure_login();
		$response = Redirect::to_secure_login();
		$this->assertEquals($url, $response->headers['Location']);
	}

	function testRedirectToNamedRouteWithStatus()
	{
		$response = Redirect::to_login(array(), 303);
		$this->assertEquals(303, $response->status);
	}

}

