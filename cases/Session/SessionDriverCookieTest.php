<?php

use \Laravel\Session\Drivers\Cookie;

include_once __DIR__.'/SessionDriverTestCase.php';

class SessionDriverCookieTest extends SessionDriverTestCase {

	static function setUpBeforeClass()
	{
		Config::set('application.key', Str::random(10));
	}

	static function tearDownAfterClass()
	{
		Config::set('application.key', '');
	}

	function setUp()
	{
		$this->driver = new Cookie;
	}

	function testDriverSavesAndLoadsAndDeletesCorrectly()
	{
		if (headers_sent())
		{
			$this->markTestSkipped('Headers have already been sent');
		}
		parent::testDriverSavesAndLoadsAndDeletesCorrectly();
	}

}
