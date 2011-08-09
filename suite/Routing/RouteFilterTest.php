<?php

class RouteFilerTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		$filters = array(
			'test' => function() {return 'test';},
			'vars' => function($var) {return $var;},
			'vars2' => function($var1, $var2) {return $var1.$var2;},
		);

		System\Routing\Filter::register($filters);
	}

	public static function tearDownAfterClass()
	{
		System\Routing\Filter::register(require APP_PATH.'filters'.EXT);
	}

	public function testCallingUndefinedFilterDoesNothing()
	{
		System\Routing\Filter::call('not-found');
		$this->assertTrue(true);
	}

	public function testFilterWithoutOverrideReturnsNull()
	{
		$this->assertNull(System\Routing\Filter::call('test'));
	}

	public function testCallingFilterWithOverrideReturnsResult()
	{
		$this->assertEquals(System\Routing\Filter::call('test', array(), true), 'test');
	}

	public function testCallingFilterWithParametersPassesParametersToFilter()
	{
		$this->assertEquals(System\Routing\Filter::call('vars', array('test'), true), 'test');
		$this->assertEquals(System\Routing\Filter::call('vars2', array('test1', 'test2'), true), 'test1test2');
	}

}