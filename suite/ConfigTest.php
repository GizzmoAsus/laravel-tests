<?php

class ConfigTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		System\Config::$items = array();
	}

	public function testHasMethodReturnsFalseWhenItemDoesntExist()
	{
		$this->assertFalse(Config::has('doesnt'));
		$this->assertFalse(Config::has('doesnt.exist'));
		$this->assertFalse(Config::has('auth::something'));
		$this->assertFalse(Config::has('auth::test.something'));
		$this->assertFalse(Config::has('auth::something.something'));
	}

	public function testHasMethodReturnsTrueWhenItemExists()
	{
		$this->assertTrue(Config::has('application'));
		$this->assertTrue(Config::has('application.url'));
		$this->assertTrue(Config::has('auth::test'));
		$this->assertTrue(Config::has('auth::test.name'));
	}

	public function testGetMethodReturnsSingleItem()
	{
		$this->assertEquals(Config::get('application.url'), 'http://localhost');
		$this->assertEquals(Config::get('db.default'), 'sqlite');
		$this->assertEquals(Config::get('auth::test.name'), 'test');
	}

	public function testGetMethodReturnsEntireArrayWhenGivenKeyWithNoDots()
	{
		$this->assertTrue(is_array(Config::get('application')));
		$this->assertTrue(is_array(Config::get('auth::test')));
		$this->assertArrayHasKey('url', Config::get('application'));
	}

	public function testGetMethodReturnsDefaultValueWhenItemDoesntExist()
	{
		$this->assertNull(Config::get('doesnt'));
		$this->assertNull(Config::get('doesnt.exist'));
		$this->assertNull(Config::get('auth::test.something'));
		$this->assertEquals(Config::get('doesnt.exist', 'test'), 'test');
		$this->assertEquals(Config::get('auth::test.something', 'test'), 'test');
		$this->assertEquals(Config::get('doesnt.exist', function() {return 'test';}), 'test');
	}

	public function testSetMethodSetsConfigurationOption()
	{
		Config::set('application.url', 'test');
		Config::set('auth::test.something', 'something');

		$this->assertEquals(Config::get('application.url'), 'test');
		$this->assertEquals(Config::get('auth::test.something'), 'something');
		$this->assertArrayHasKey('timezone', Config::get('application'));

		Config::$items = require CONFIG_PATH.'application'.EXT;
	}

	/**
	 * @expectedException Exception
	 */
	public function testSetMethodThrowsExceptionIfFileDoesntExist()
	{
		Config::set('auth::something.something', 'test');
	}

	/**
	 * @expectedException Exception
	 */
	public function testSetMethodThrowsExceptionIfTryingToSetEntireArray()
	{
		Config::set('auth::test', array('test' => 'test'));
	}

}