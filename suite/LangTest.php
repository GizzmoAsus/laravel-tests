<?php

class LangTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		Lang::$lines = array();
	}

	public function testLineMethodReturnsNewLangInstance()
	{
		$this->assertInstanceOf('System\\Lang', Lang::line('validation.required'));
		$this->assertEquals(Lang::line('validation.required')->key, 'validation.required');
		$this->assertArrayHasKey('name', Lang::line('validation.required', array('name' => 'test'))->replacements);
	}

	public function testGetMethodReturnsStringContentOfLine()
	{
		$messages = require APP_PATH.'lang/en/validation'.EXT;
		$this->assertEquals(Lang::line('validation.required')->get(), $messages['required']);
	}

	public function testGetMethodReturnsDefaultWhenLineDoesntExist()
	{
		$this->assertNull(Lang::line('doesnt.exist')->get());
		$this->assertEquals(Lang::line('doesnt.exist')->get(null, 'test'), 'test');
		$this->assertEquals(Lang::line('doesnt.exist')->get(null, function() {return 'test';}), 'test');
	}

	public function testGetMethodMakesReplacements()
	{
		Lang::$lines['application']['envalidation']['required'] = ':name :size';
		$this->assertEquals(Lang::line('validation.required', array('name' => 'test', 'size' => 100, 'foo' => 'bar'))->get(), 'test 100');
	}

	public function testStringCastingGivesLanguageLine()
	{
		$messages = require APP_PATH.'lang/en/validation'.EXT;
		$this->assertEquals((string) Lang::line('validation.required'), $messages['required']);
	}

	/**
	 * @expectedException Exception
	 */
	public function testExceptionIsThrownIfInvalidKeyIsGiven()
	{
		Lang::line('validation')->get();
	}

}