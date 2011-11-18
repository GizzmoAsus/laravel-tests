<?php

abstract class CacheDriverTestCase extends PHPUnit_Framework_TestCase {

	protected $driver = null;

	function testHasReturnsFalseForUnknownKeys()
	{
		$this->assertFalse($this->driver->has('unknown'));
	}

	function testHasReturnsTrueForKnownKeys()
	{
		$this->driver->put('name', 'Phill', 15);
		$this->assertTrue($this->driver->has('name'));

		$this->driver->forget('name'); // tearDown
	}

	function testGetReturnsNullForUnknownKeys()
	{
		$this->assertNull($this->driver->get('unknown'));
	}

	function testGetReturnsDefaultForUnknownKeys()
	{
		$this->assertEquals('something', $this->driver->get('unknown', 'something'));
	}

	function testDriverPutsAndGetsAndForgetsCorrectly()
	{
		$this->driver->put('name', 'Phill', 15);
		$this->assertEquals('Phill', $this->driver->get('name'));

		$this->driver->put('name', 'Taylor', 15);
		$this->assertEquals('Taylor', $this->driver->get('name'));

		$this->driver->forget('name');
		$this->assertNull($this->driver->get('name'));
	}

	function testRemeberRetrievesStoredValues()
	{
		$this->driver->put('name', 'Phill', 15);
		$this->assertEquals('Phill', $this->driver->remember('name', 'Taylor', 15));

		$this->driver->forget('name'); // tearDown
	}

	function testRememberStoresAndReturnsDefaultValue()
	{
		$this->assertNull($this->driver->get('name')); // precondition

		$this->assertEquals('Taylor', $this->driver->remember('name', 'Taylor', 15));
		$this->assertEquals('Taylor', $this->driver->get('name'));

		$this->driver->forget('name'); // tearDown
	}

	function testRememberStoresAndReturnsCallbackDefaultValues()
	{
		$this->assertNull($this->driver->get('name')); // precondition

		$callback = function()
		{
			return 'Phill';
		};

		$this->assertEquals('Phill', $this->driver->remember('name', $callback, 15));
		$this->assertEquals('Phill', $this->driver->get('name'));

		$this->driver->forget('name'); // tearDown
	}

	// TODO Test time based cache things

}
