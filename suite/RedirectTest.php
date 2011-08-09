<?php

class RedirectTest extends PHPUnit_Framework_TestCase {

	public function testCreationSetsResponseVariable()
	{
		$redirect = new Redirect('test');
		$this->assertEquals($redirect->response, 'test');
	}

	public function testToMethodCreatesRedirectInstanceWithProperHeadersAndStatus()
	{
		$this->assertInstanceOf('System\\Redirect', Redirect::to('http://google.com'));
		$this->assertEquals(Redirect::to('http://google.com')->response->status, 302);
		$this->assertEquals(Redirect::to('http://google.com')->response->headers['Location'], 'http://google.com');
	}

}