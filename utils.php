<?php

class Utils {

	/**
	 * Recursively remove a directory.
	 *
	 * @param  string  $directory
	 * @return void
	 */
	public static function rrmdir($directory)
	{
		if (is_dir($directory)) 
		{ 
		 	$objects = scandir($directory);

		 	foreach ($objects as $object) 
		 	{ 
		   		if ($object != "." && $object != "..") 
		   		{ 
		     		if (filetype($directory."/".$object) == "dir") static::rrmdir($directory."/".$object); else unlink($directory."/".$object); 
		   		} 
		 	} 

		 	reset($objects); 
		 	rmdir($directory); 
		} 
	}

	/**
	 * Set the default database connection to the fixture database.
	 *
	 * @return void
	 */
	public static function setup_db()
	{
		$connections = array('sqlite' => array('driver' => 'sqlite', 'database' => FIXTURE_PATH.'fixture.sqlite'));
		System\Config::set('db.connections', $connections);
	}

}