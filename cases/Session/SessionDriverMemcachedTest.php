<?php

use \Laravel\Cache\Manager as Cache;
use \Laravel\Session\Drivers\Memcached;

include_once __DIR__.'/SessionDriverTestCase.php';

class SessionDriverMemcachedTest extends SessionDriverTestCase {

	function setUp()
	{
		if ( ! extension_loaded('memcache'))
		{
			$this->markTestSkipped('Memcache extension is missing');
		}

		$this->driver = new Memcached(Cache::driver('memcached'));
	}

}
