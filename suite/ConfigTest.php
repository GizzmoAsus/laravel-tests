<?php

class ConfigTest extends PHPUnit_Framework_TestCase {

	public function testHasMethodReturnsFalseWhenItemDoesntExist()
	{
		$this->assertFalse(Config::has('doesnt'));
		$this->assertFalse(Config::has('doesnt.exist'));
	}

	public function testHasMethodReturnsTrueWhenItemExists()
	{
		$this->assertTrue(Config::has('application'));
		$this->assertTrue(Config::has('application.url'));
	}

	public function testGetMethodReturnsSingleItem()
	{
		$this->assertEquals(Config::get('application.url'), 'http://localhost');
		$this->assertEquals(Config::get('db.default'), 'sqlite');
	}

	public function testGetMethodReturnsEntireArrayWhenGivenKeyWithNoDots()
	{
		$this->assertTrue(is_array(Config::get('application')));
		$this->assertArrayHasKey('url', Config::get('application'));
	}

	public function testGetMethodReturnsDefaultValueWhenItemDoesntExist()
	{
		$this->assertNull(Config::get('doesnt'));
		$this->assertNull(Config::get('doesnt.exist'));
		$this->assertEquals(Config::get('doesnt.exist', 'test'), 'test');
		$this->assertEquals(Config::get('doesnt.exist', function() {return 'test';}), 'test');
	}

	public function testSetMethodSetsConfigurationOption()
	{
		Config::set('application.url', 'test');

		$this->assertEquals(Config::get('application.url'), 'test');
		$this->assertArrayHasKey('timezone', Config::get('application'));

		Config::$items = require CONFIG_PATH.'application'.EXT;
	}

}