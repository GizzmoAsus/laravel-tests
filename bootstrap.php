<?php

/*
|--------------------------------------------------------------------------
| Installation Paths
|--------------------------------------------------------------------------
*/

$application = 'framework/application';

$laravel     = 'framework/laravel';

$public      = 'framework/public';

define('FIXTURE_PATH', __DIR__.'/Fixtures/');

/*
|--------------------------------------------------------------------------
| Bootstrap The Laravel Core
|--------------------------------------------------------------------------
*/

require realpath($laravel).'/bootstrap/core.php';