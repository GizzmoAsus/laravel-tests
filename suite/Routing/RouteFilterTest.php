<?php

class RouteFilerTest extends PHPUnit_Framework_TestCase {

	/**
	 * Setup the test environment.
	 */
	public static function setUpBeforeClass()
	{
		$filters = array(
			'test' => function() {return 'test';},
			'vars' => function($var) {return $var;},
			'vars2' => function($var1, $var2) {return $var1.$var2;},
		);

		System\Routing\Filter::register($filters);
	}

	/**
	 * Tear down the test environment.
	 */
	public static function tearDownAfterClass()
	{
		System\Routing\Filter::register(require ROUTE_PATH.'filters'.EXT);
	}

	/**
	 * Calling an undefined filter should throw an exception.
	 * 
	 * @expectedException Exception
	 */
	public function testCallingUndefinedFilterThrowsException()
	{
		System\Routing\Filter::call('not-found');
	}

	/**
	 * Calling a route filter without overriding should return NULL.
	 */
	public function testFilterWithoutOverrideReturnsNull()
	{
		$this->assertNull(System\Routing\Filter::call('test'));
	}

	/**
	 * Calling a route filter with an override should return the result of that filter.
	 */
	public function testCallingFilterWithOverrideReturnsResult()
	{
		$this->assertEquals(System\Routing\Filter::call('test', array(), true), 'test');
	}

	/**
	 * Calling a route filter with parameters should result in the parameters being passed
	 * to the route filter.
	 */
	public function testCallingFilterWithParametersPassesParametersToFilter()
	{
		$this->assertEquals(System\Routing\Filter::call('vars', array('test'), true), 'test');
		$this->assertEquals(System\Routing\Filter::call('vars2', array('test1', 'test2'), true), 'test1test2');
	}

}