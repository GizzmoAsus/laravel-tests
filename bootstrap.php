<?php

// --------------------------------------------------------------
// Define the framework paths.
// --------------------------------------------------------------
define('BASE_PATH', realpath('../laravel').'/');
define('APP_PATH', realpath('../laravel/application').'/');
define('SYS_PATH', realpath('../laravel/system').'/');
define('CONFIG_PATH', APP_PATH.'config/');
define('PACKAGE_PATH', APP_PATH.'packages/');
define('PUBLIC_PATH', BASE_PATH.'public/');
define('PUBLIC_PATH', BASE_PATH.'public/');

// --------------------------------------------------------------
// Define the fixture path.
// --------------------------------------------------------------
define('FIXTURE_PATH', realpath('fixtures').'/');

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
// Load the test utilities.
// --------------------------------------------------------------
require 'utils'.EXT;

// --------------------------------------------------------------
// Register the auto-loader.
// --------------------------------------------------------------
spl_autoload_register(require SYS_PATH.'loader'.EXT);
