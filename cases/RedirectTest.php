<?php namespace Laravel; use PHPUnit_Framework_TestCase;

/*
 * Test the Rediect class, assuming URL performs correctly
 */

class RedirectTest extends PHPUnit_Framework_TestCase {

	function test_redirect_to_string()
	{
		$url = URL::to('test');
		$response = Redirect::to('test');
		$this->assertEquals(302, $response->status);
		$this->assertEquals($url, $response->headers['Location']);
	}

	function test_redirect_to_secure_string()
	{
		$url = URL::to_secure('test');
		$response = Redirect::to_secure('test');
		$this->assertEquals($url, $response->headers['Location']);
	}

	function test_redirect_with_status()
	{
		$response = Redirect::to('test', 303); // See Other
		$this->assertEquals(303, $response->status);
	}

	// Exception: Error resolving [laravel.routing.router]. No resolver has been registered in the container.

	// function test_redirect_to_named_route()
	// {
	//     $url = URL::to_login();
	//     $response = Redirect::to_login();
	//     $this->assertEquals($url, $response->headers['Location']);
	// }

	// Exception: Error resolving [laravel.routing.router]. No resolver has been registered in the container.

	// function test_redirect_to_secure_named_route()
	// {
	//     $url = URL::to_secure_login();
	//     $response = Redirect::to_secure_login();
	//     $this->assertEquals($url, $response->headers['Location']);
	// }

	// Exception: Error resolving [laravel.routing.router]. No resolver has been registered in the container.

	// function test_redirect_to_named_route_with_status()
	// {
	//     $response = Redirect::to_login(array(), 303);
	//     $this->assertEquals(303, $response->status);
	// }

	// PHPUnit Exception: Cannot expect a base Exception

	// /*
	//  * @expectedException RuntimeException
	//  * @expectedExceptionMessage A session driver must be set before setting flash data.
	//  */
	// function test_redirect_with_flash_throws_session_exception()
	// {
	//     $driver = Config::get('session.driver');
	//     Config::set('session.driver', '');
	//     try {
	//         Redirect::to('test')->with('flash', 'Your message');
	//     }
	//     catch (Exception $e) {
	//         Config::set('session.driver', $driver);
	//         throw $e;
	//     }
	// }

	// Exception: Error resolving [laravel.session]. No resolver has been registered in the container.

	// function test_redirect_with_flash()
	// {
	//     $driver = Config::get('session.driver');
	//     Config::set('session.driver', 'file');

	//     $response = Redirect::to('test')->with('flash', 'Your message');
	//     $flash = IoC::core('session')->get('flash');
	//     $this->assertEquals('Your message', $flash);

	//     Config::set('session.driver', $driver);
	// }

}

