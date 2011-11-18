<?php

use \Laravel\Cache\Drivers\Memcached;

include_once __DIR__.'/CacheDriverTestCase.php';

class CacheDriverMemcachedTest extends CacheDriverTestCase {

	function setUp()
	{
		if ( ! extension_loaded('memcache'))
		{
			$this->markTestSkipped('Memcache extension is missing');
		}
		$this->driver = new Memcached(\Laravel\Memcached::instance(), 'laravel');
	}

}
