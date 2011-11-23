<?php

class ResponseTest extends PHPUnit_Framework_TestCase {

	public function testMakeMethodReturnsResponseInstance()
	{
		$this->assertInstanceOf('Laravel\\Response', Response::make('test'));
	}

	public function testMakeMethodSetsContentAndStatus()
	{
		$response = Response::make('test');

		$this->assertEquals('test', $response->content);
		$this->assertEquals(200, $response->status);
		$this->assertEquals(404, Response::make('test', 404)->status);
	}

	public function testStatusMethodSetsStatus()
	{
		$response = Response::make('test');
		$this->assertEquals(200, $response->status);

		$response->status(404);
		$this->assertEquals(404, $response->status);
	}

	public function testErrorMethodGetsProperViewAndStatus()
	{
		$response = Response::error('404');

		$this->assertEquals(404, $response->status);
		$this->assertEquals('error/404', $response->content->view);
	}

	public function testViewMethodReturnsResponseInstance()
	{
		$this->assertInstanceOf('Laravel\\Response', Response::view('home/index'));
	}

	public function testViewMethodGetsProperViewAndStatus()
	{
		$response = Response::view('home/index');

		$this->assertEquals('home/index', $response->content->view);
		$this->assertEquals(200, $response->status);
	}

	public function testOfMethodReturnsResponseInstance()
	{
		$this->assertInstanceOf('Laravel\\Response', Response::of('home'));
	}

	public function testOfMethodGetsProperViewAndStatus()
	{
		$response = Response::of('home');

		$this->assertEquals('home.index', $response->content->view);
		$this->assertEquals(200, $response->status);
	}

	public function testMagicOfMethodReturnsResponseInstance()
	{
		$this->assertInstanceOf('Laravel\\Response', Response::of_home());
	}

	public function testMagicOfMethodGetsProperViewAndStatus()
	{
		$response = Response::of_home();

		$this->assertEquals('home.index', $response->content->view);
		$this->assertEquals(200, $response->status);
	}

	public function testRenderMethodReturnsContentOfResponse()
	{
		$this->assertEquals('test', Response::make('test')->render());
	}

	public function testRenderMethodReturnsContentOfView()
	{
		$this->assertEquals(View::make('home/index')->render(), Response::view('home/index')->render());
	}

	public function testSendMethodEchosContentOfResponse()
	{
		$this->expectOutputString('test');
		Response::make('test')->send();
	}

	public function testHeaderMethodAddsToHeaderArray()
	{
		$this->assertArrayHasKey('Content-Type', Response::make('test')->header('Content-Type', 'text/plain')->headers);
	}

	public function testHeaderMethodReturnsResponseInstance()
	{
		$this->assertInstanceOf('Laravel\\Response', Response::make('test')->header('Content-Type', 'text/plain'));
	}


	/**
	 * @expectedException \BadMethodCallException
	 */
	public function testBadMethodCallExceptionIsThrown()
	{
		Response::something();
	}

}

