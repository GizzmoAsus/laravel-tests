<?php namespace Laravel; use PHPUnit_Framework_TestCase;

/*
 * Test the Rediect class, assuming URL performs correctly
 */

class RedirectTest extends PHPUnit_Framework_TestCase {

	function testRedirectToString()
	{
		$url = URL::to('test');
		$response = Redirect::to('test');
		$this->assertEquals(302, $response->status);
		$this->assertEquals($url, $response->headers['Location']);
	}

	function testRedirectToSecureString()
	{
		$url = URL::to_secure('test');
		$response = Redirect::to_secure('test');
		$this->assertEquals($url, $response->headers['Location']);
	}

	function testRedirectWithStatus()
	{
		$response = Redirect::to('test', 303); // See Other
		$this->assertEquals(303, $response->status);
	}

	/**
	 * @expectedException LogicException
	 * @expectedExceptionMessage A session driver must be set before setting flash data.
	 */
	function testRedirectWithFlashThrowsSessionDriverException()
	{
		Config::set('session.driver', '');
		Redirect::to('test')->with('flash', 'Your message');
	}

	// Exception: Error resolving [laravel.session]. No resolver has been registered in the container.

	function testRedirectWithFlash()
	{
		Config::set('application.key', 'test');
		Config::set('session.driver', 'mock');

		$driver = $this->getMock('Laravel\\Session\\Drivers\\Driver');

		$session = $this->getMock('Laravel\\Session\\Payload', array(), array($driver, 'test'));
		$session->expects($this->once())
			->method('flash')
			->with($this->equalTo('flash', 0),
			   $this->equalTo('Your message', 1));
		IoC::instance('laravel.session', $session);

		$response = Redirect::to('test')->with('flash', 'Your message');

		unset($driver, $session);
		Config::set('application.key', '');
		Config::set('session.driver', '');
	}
	
	
	private function getMockDriver()
	{
		return $this->getMock('Laravel\\Session\\Drivers\\Driver');
	}

}

