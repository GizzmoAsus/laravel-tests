<?php

class ArrTest extends PHPUnit_Framework_TestCase {

	public function testReturnsDefaultWhenItemNotPresent()
	{
		$this->assertNull(System\Arr::get(array(), 'name'));
		$this->assertEquals(System\Arr::get(array(), 'name', 'test'), 'test');
		$this->assertEquals(System\Arr::get(array(), 'name', function() {return 'test';}), 'test');
	}

	public function testReturnsItemWhenPresentInArray()
	{
		$this->assertEquals(System\Arr::get(array('name' => 'test'), 'name'), 'test');

		$nested = array('name' => array('is' => array('something' => 'taylor')));

		$this->assertArrayHasKey('is', System\Arr::get($nested, 'name'));
		$this->assertArrayHasKey('something', System\Arr::get($nested, 'name.is'));
		$this->assertEquals(System\Arr::get($nested, 'name.is.something'), 'taylor');
	}

}