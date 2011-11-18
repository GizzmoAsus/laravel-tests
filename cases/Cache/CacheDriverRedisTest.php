<?php

use \Laravel\Cache\Drivers\Redis;

include_once __DIR__.'/CacheDriverTestCase.php';

class CacheDriverRedisTest extends CacheDriverTestCase {

	static $has_redis = true;

	static function setUpBeforeClass()
	{
		try
		{
			$redis = \Laravel\Redis::db();
			$redis->get('test');
		}
		catch (RuntimeException $e)
		{
			static::$has_redis = false;
		}
	}

	function setUp()
	{
		if ( ! static::$has_redis)
		{
			$this->markTestSkipped('Redis server is missing');
		}
		$this->driver = new Redis(\Laravel\Redis::db());
	}

}
