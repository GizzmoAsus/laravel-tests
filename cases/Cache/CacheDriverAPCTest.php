<?php

use \Laravel\Cache\Drivers\APC;

include_once __DIR__.'/CacheDriverTestCase.php';

class CacheDriverAPCTest extends CacheDriverTestCase {

	function setUp()
	{
		if ( ! extension_loaded('apc'))
		{
			$this->markTestSkipped('APC extension is missing');
		}
		$this->driver = new APC('laravel');
	}

}
