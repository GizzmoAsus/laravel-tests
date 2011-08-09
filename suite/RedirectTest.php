<?php

class RedirectTest extends PHPUnit_Framework_TestCase {

	public function testCreationSetsResponseVariable()
	{
		$redirect = new Redirect('test');
		$this->assertEquals($redirect->response, 'test');
	}

	public function testToMethodCreatesRedirectInstance()
	{
		$this->assertInstanceOf('System\\Redirect', Redirect::to('http://google.com'));
	}

	public function testToMethodSetsProperStatus()
	{
		$this->assertEquals(Redirect::to('http://google.com')->response->status, 302);
		$this->assertEquals(Redirect::to('http://google.com', 301)->response->status, 301);		
	}

	public function testToMethodSetsProperLocation()
	{
		$this->assertEquals(Redirect::to('http://google.com')->response->headers['Location'], 'http://google.com');
		$this->assertEquals(Redirect::to('something', 301, 'location', true)->response->headers['Location'], 'https://localhost/index.php/something');
	}

	public function testToMethodCanCreateHTTPSRedirects()
	{
		$this->assertEquals(Redirect::to('http://google.com', 302, 'refresh')->response->headers['Refresh'], '0;url=http://google.com');
	}

	public function testToSecureMethodCreatesHTTPSRedirect()
	{
		$this->assertEquals(Redirect::to_secure('something')->response->headers['Location'], 'https://localhost/index.php/something');
	}

	public function testWithMethodSetsSessionFlashData()
	{
		Config::set('session.driver', 'apc');

		Redirect::to('something')->with('name', 'test');
		$this->assertEquals(System\Session::$session['data'][':new:name'], 'test');

		Config::set('session.driver', '');
		System\Session::$session = null;
	}

	/**
	 * @expectedException Exception
	 */
	public function testWithMethodThrowsExceptionWhenSessionDriverNotSet()
	{
		Redirect::to('something')->with('name', 'test');
	}

}