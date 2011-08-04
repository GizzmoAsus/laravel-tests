<?php

class LangTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		Lang::$lines = array();
	}

	public function testLineMethodReturnsNewLangInstance()
	{
		$this->assertInstanceOf('System\\Lang', Lang::line('validation.required'));
		$this->assertInstanceOf('System\\Lang', Lang::line('auth::messages.welcome'));
		$this->assertEquals(Lang::line('validation.required')->key, 'validation.required');
		$this->assertArrayHasKey('name', Lang::line('validation.required', array('name' => 'test'))->replacements);
	}

	public function testGetMethodReturnsStringContentOfLine()
	{
		$messages = require APP_PATH.'lang/en/validation'.EXT;
		$module_messages = require MODULE_PATH.'auth/lang/en/messages'.EXT;

		$this->assertEquals(Lang::line('validation.required')->get(), $messages['required']);
		$this->assertEquals(Lang::line('auth::messages.welcome')->get(), $module_messages['welcome']);
	}

	public function testGetMethodReturnsDefaultWhenLineDoesntExist()
	{
		$this->assertNull(Lang::line('doesnt.exist')->get());
		$this->assertNull(Lang::line('auth::doesnt.exist')->get());
		$this->assertNull(Lang::line('auth::messages.something')->get());
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