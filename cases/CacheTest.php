<?php namespace Laravel\Cache\Drivers;

use \Laravel\Config;
use \Laravel\Cache\Manager as Cache;

class CacheTest extends \PHPUnit_Framework_TestCase {

	static function setUpBeforeClass()
	{
		Config::load('cache');
	}

	/**
	 * @dataProvider driverNameClassProvider
	 */
	function testCacheDriverReturnsCorrectDriver($name, $class)
	{
		if ($name == 'memcached' and ! class_exists('Memcache'))
		{
			$this->markTestSkipped('Memcache is missing');
		}

		$driver = Cache::driver($name);
		$this->assertInstanceOf($class, $driver);
	}

	/**
	 * @expectedException \DomainException
	 */
	function testCacheDriverThrowsExceptionOnUnknownDriver()
	{
		$driver = Cache::driver('unknown');
	}

	/**
	 * @dataProvider driverNameClassProvider
	 */
	function testCacheDriverReturnsDefaultDriver($name, $class)
	{
		if ($name == 'memcached' and ! class_exists('Memcache'))
		{
			$this->markTestSkipped('Memcache is missing');
		}

		Config::set('cache.driver', $name);
		$driver = Cache::driver();
		$this->assertInstanceOf($class, $driver);
	}

	/**
	 * @dataProvider staticMethodProvider
	 */
	function testCacheForwardsStaticCallsToDefaultDriver($method, $params)
	{
		$driver = $this->getMock('\Laravel\CAche\Drivers\DummyDriver');
		$driver->expects($this->once())
			->method($method);

		$cache = $this->getMockCacheClassWithDriver($driver);

		forward_static_call_array(array($cache, $method), $params);
	}

	function testDriverGetCallsDriverRetrieve()
	{
		$map = array(
			array('name', 'Phill'),
			array('email', null),
		);

		$driver = $this->getMockForAbstractClass('\Laravel\Cache\Drivers\Driver');
		$driver->expects($this->exactly(2))
			->method('retrieve')
			->will($this->returnValueMap($map));

		$this->assertEquals('Phill', $driver->get('name'));
		$this->assertNull($driver->get('email'));
	}

	function testDriverGetWillReturnDefault()
	{
		$driver = $this->getMockForAbstractClass('\Laravel\Cache\Drivers\Driver');
		$driver->expects($this->any())
			->method('retrieve')
			->will($this->returnValue(null));

		$this->assertEquals('Taylor', $driver->get('name', 'Taylor'));
		$this->assertEquals('me@phills.me.uk', $driver->get('email', 'me@phills.me.uk'));
	}

	function testDriverRememberReturnsCachedItem()
	{
		$driver = $this->getMockForAbstractClass('\Laravel\Cache\Drivers\Driver');
		$driver->expects($this->any())
			->method('retrieve')
			->with('name')
			->will($this->returnValue('Phill'));

		$this->assertEquals('Phill', $driver->remember('name', 'Taylor', 15));
	}

	function testDriverRememberPutsDefault()
	{
		$driver = $this->getMockForAbstractClass('\Laravel\Cache\Drivers\Driver');
		$driver->expects($this->any())
			->method('retrieve')
			->will($this->returnValue(null));
		$driver->expects($this->atLeastOnce())
			->method('put')
			->with('name', 'Taylor', 15);

		$this->assertEquals('Taylor', $driver->remember('name', 'Taylor', 15));
	}

	function testDriverRememberCallsCallback()
	{
		$driver = $this->getMockForAbstractClass('\Laravel\Cache\Drivers\Driver');
		$driver->expects($this->any())
			->method('retrieve')
			->will($this->returnValue(null));

		$callback = function()
		{
			return 'Matt';
		};

		$this->assertEquals('Matt', $driver->remember('name', $callback, 15));
	}

	/**
	 *
	 * Data Providers
	 */
	function driverNameClassProvider()
	{
		return array(
			array('apc', '\Laravel\Cache\Drivers\APC'),
			array('file', '\Laravel\Cache\Drivers\File'),
			array('memcached', '\Laravel\Cache\Drivers\Memcached'),
			array('redis', '\Laravel\Cache\Drivers\Redis'),
		);
	}

	function staticMethodProvider()
	{
		return array(
			// abstract
			array('has', array('key')),
			array('put', array('key', 'value', 15)),
			array('forget', array('key')),
			// other
			array('get', array('key')),
			array('get', array('key', 'default')),
			array('remember', array('key', 'value', 15)),
		);
	}

	function getMockCacheClassWithDriver($driver)
	{
		$cache = $this->getMockClass('\Laravel\Cache\Manager', array('driver'));
		$cache::staticExpects($this->any())
			->method('driver')
			->with(null)
			->will($this->returnValue($driver));
		return $cache;
	}

}

class DummyDriver extends Driver {

	function has($name)
	{
		return FALSE;
	}

	protected function retrieve($key)
	{
		return NULL;
	}

	function put($name, $value, $minutes)
	{
		// return void;
	}

	function forget($key)
	{
		// return void;
	}

}
