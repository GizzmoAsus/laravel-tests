<?php

use \Laravel\Cache\Manager as Cache;
use \Laravel\Session\Drivers\APC;

include_once __DIR__.'/SessionDriverTestCase.php';

class SessionDriverAPCTest extends SessionDriverTestCase {

	function setUp()
	{
		if ( ! extension_loaded('apc'))
		{
			$this->markTestSkipped('APC extension is missing');
		}
		$this->driver = new APC(Cache::driver('apc'));
	}

}
