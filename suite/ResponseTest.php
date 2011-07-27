<?php

class ResponseTest extends PHPUnit_Framework_TestCase {

	public function testMakeMethodReturnsResponseInstance()
	{
		$this->assertInstanceOf('System\\Response', Response::make('test'));
	}

	public function testMakeMethodSetsContentAndStatus()
	{
		$response = Response::make('test');

		$this->assertEquals($response->content, 'test');
		$this->assertEquals($response->status, 200);
		$this->assertEquals(Response::make('test', 404)->status, 404);
	}

	public function testErrorMethodGetsProperViewAndStatus()
	{
		$this->assertEquals(Response::error('404')->status, 404);
		$this->assertEquals(Response::error('404')->content->view, 'error/404');
	}

	public function testPrepareMethodReturnsResponseInstance()
	{
		$this->assertInstanceOf('System\\Response', Response::prepare('test'));
		$this->assertInstanceOf('System\\Response', Response::prepare(View::make('home/index')));
		$this->assertInstanceOf('System\\Response', Response::prepare(Response::make('test')));
	}

	public function testSendMethodEchosContentOfResponse()
	{
		ob_start();
		Response::make('test')->send();
		$this->assertEquals('test', ob_get_clean());

		ob_start();
		Response::make(View::make('home/index'))->send();
		$this->assertEquals(View::make('home/index')->get(), ob_get_clean());
	}

	public function testHeaderMethodAddsToHeaderArray()
	{
		$this->assertArrayHasKey('Content-Type', Response::make('test')->header('Content-Type', 'text/plain')->headers);
	}

	public function testHeaderMethodReturnsResponseInstance()
	{
		$this->assertInstanceOf('System\\Response', Response::make('test')->header('Content-Type', 'text/plain'));
	}

	public function testIsRedirectMethodIdentifiesRedirectResponses()
	{
		$this->assertTrue(Response::make('test', 301)->is_redirect());
		$this->assertTrue(Response::make('test', 302)->is_redirect());
		$this->assertFalse(Response::make('test', 200)->is_redirect());
		$this->assertFalse(Response::make('test', 404)->is_redirect());
	}

	public function testCastingResponseToStringReturnsStringContent()
	{
		$this->assertTrue(is_string((string) Response::make('test')));
		$this->assertEquals('test', (string) Response::make('test'));
		$this->assertEquals(View::make('home/index')->get(), (string) Response::make(View::make('home/index')));
	}

}