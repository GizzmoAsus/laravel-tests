<?php

class ViewTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		System\Config::set('application.modules', array('auth'));
	}

	public function tearDown()
	{
		System\Config::set('application.modules', array());
	}

	public function testConstructorSetsViewNameAndData()
	{
		$view = new System\View('home/index', array('name' => 'test'));

		$this->assertEquals($view->view, 'home/index');
		$this->assertEquals($view->data, array('name' => 'test'));

		$view = new System\View('home/index');
		$this->assertEquals($view->data, array());
	}

	public function testMakeMethodReturnsNewViewInstance()
	{
		$this->assertInstanceOf('System\\View', System\View::make('home/index'));
		$this->assertInstanceOf('System\\View', System\View::make('home.index'));
		$this->assertInstanceOf('System\\View', System\View::make('auth::home'));
		$this->assertInstanceOf('System\\View', System\View::make('auth::partials/test'));
		$this->assertInstanceOf('System\\View', System\View::make('auth::partials.test'));
	}

	public function testBindMethodAddsItemToViewData()
	{
		$view = System\View::make('home/index')->bind('name', 'test');
		$this->assertEquals($view->data, array('name' => 'test'));
	}

	public function testBoundViewDataCanBeRetrievedThroughMagicMethods()
	{
		$view = System\View::make('home/index')->bind('name', 'test');

		$this->assertTrue(isset($view->name));
		$this->assertEquals($view->name, 'test');

		unset($view->name);
		$this->assertFalse(isset($view->name));
	}

	public function testPartialMethodPutsAViewInstanceInTheViewData()
	{
		$view = System\View::make('home/index')->partial('partial', 'home/index');
		$this->assertInstanceOf('System\\View', $view->partial);
		$this->assertEquals($view->partial->view, 'home/index');

		$view = System\View::make('home/index')->partial('partial', 'auth::home');
		$this->assertInstanceOf('System\\View', $view->partial);
		$this->assertEquals($view->partial->view, 'home');		
	}

	public function testGetMethodReturnsStringContentOfView()
	{
		$this->assertTrue(is_string(System\View::make('home/index')->get()));
		$this->assertTrue(is_string(System\View::make('auth::home')->get()));
		$this->assertTrue(is_string(System\View::make('auth::partials.test')->get()));

		$this->assertEquals(System\View::make('auth::home')->get(), 'Auth');
		$this->assertEquals(System\View::make('auth::partials.test')->get(), 'AuthPartial');
	}

	public function testComposerIsCalledWhenItIsDefinedForView()
	{
		System\View::$composers['application']['home.index'] = function($view) {$view->bind('name', 'test');};
		$this->assertEquals(System\View::make('home.index')->name, 'test');
		System\View::$composers = null;
	}

	public function testViewCanBeCreatedByName()
	{
		$this->assertInstanceOf('System\\View', System\View::of_home());
	}

	/**
	 * @expectedException Exception
	 */
	public function testAttemptingToCreateUndefinedNamedViewThrowsException()
	{
		System\View::of_something();
	}

	/**
	 * @expectedException Exception
	 */
	public function testExceptionIsThrownWhenViewDoesntExist()
	{
		System\View::make('doesnt-exist')->get();
	}

}