<?php
/**
 * Unit Tests For The Laravel PHP Framework.
 *
 * @package  Laravel
 * @version  1.3.0
 * @author   Taylor Otwell
 * @link     http://laravel.com
 */

// --------------------------------------------------------------
// Define the core framework paths.
// --------------------------------------------------------------
define('APP_PATH', realpath('../laravel/application').'/');
define('BASE_PATH', realpath('../laravel').'/');
define('PUBLIC_PATH', realpath('../laravel/public').'/');
define('SYS_PATH', realpath('../laravel/system').'/');

// --------------------------------------------------------------
// Define various other framework paths.
// --------------------------------------------------------------
define('CACHE_PATH', APP_PATH.'storage/cache/');
define('CONFIG_PATH', APP_PATH.'config/');
define('DATABASE_PATH', APP_PATH.'storage/db/');
define('LANG_PATH', APP_PATH.'lang/');
define('LIBRARY_PATH', APP_PATH.'libraries/');
define('MODEL_PATH', APP_PATH.'models/');
define('PACKAGE_PATH', APP_PATH.'packages/');
define('ROUTE_PATH', APP_PATH.'routes/');
define('SESSION_PATH', APP_PATH.'storage/sessions/');
define('SYS_CONFIG_PATH', SYS_PATH.'config/');
define('VIEW_PATH', APP_PATH.'views/');

// --------------------------------------------------------------
// Define the fixture path.
// --------------------------------------------------------------
define('FIXTURE_PATH', realpath('fixtures').'/');
define('MODULE_PATH', FIXTURE_PATH.'modules/');

// --------------------------------------------------------------
// Define the PHP file extension.
// --------------------------------------------------------------
define('EXT', '.php');

// --------------------------------------------------------------
// Load the classes used by the auto-loader.
// --------------------------------------------------------------
require SYS_PATH.'config'.EXT;
require SYS_PATH.'arr'.EXT;

// --------------------------------------------------------------
// Register the auto-loader.
// --------------------------------------------------------------
spl_autoload_register(function($class) 
{
	$file = strtolower(str_replace('\\', '/', $class));

	if (array_key_exists($class, $aliases = System\Config::get('aliases')))
	{
		return class_alias($aliases[$class], $class);
	}

	foreach (array(BASE_PATH, MODEL_PATH, LIBRARY_PATH) as $directory)
	{
		if (file_exists($path = $directory.$file.EXT))
		{
			require $path;

			return;
		}
	}
});

// --------------------------------------------------------------
// Load the test utilities.
// --------------------------------------------------------------
require 'utils'.EXT;