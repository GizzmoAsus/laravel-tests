<?php

use \Laravel\Session\Drivers\File;

include_once __DIR__.'/SessionDriverTestCase.php';

class SessionDriverFileTest extends SessionDriverTestCase {

	static protected $path;

	static function setUpBeforeClass()
	{
		static::$path = tempnam(sys_get_temp_dir(), 'Session');
		unlink(static::$path);

		static::$path .= '/';
		mkdir(static::$path, 0700);
	}

	function setUp()
	{
		$this->driver = new File(static::$path);
	}

	static function tearDownAfterClass()
	{
		static::rrmdir(static::$path);
		static::$path = null;
	}

	/**
	 * Utility method to recursively delete a directory
	 */
	private static function rrmdir($dir)
	{
		if (is_dir($dir))
		{
			$objects = scandir($dir);
			foreach ($objects as $object)
			{
				if ($object != "." && $object != "..")
				{
					if (filetype($dir."/".$object) == "dir") static::rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}

}
