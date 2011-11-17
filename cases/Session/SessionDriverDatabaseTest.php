<?php

use \Laravel\Session\Drivers\Database;

include_once __DIR__.'/SessionDriverTestCase.php';

class SessionDriverDatabaseTest extends SessionDriverTestCase {

	static function setUpBeforeClass()
	{
		copy(FIXTURE_PATH.'session.sqlite', STORAGE_PATH.'database/session.sqlite');
		Config::set('database.connections.session', array(
			'driver'   => 'sqlite',
			'database' => 'session',
		));
	}

	function setUp()
	{
		$this->driver = new Database(\Laravel\Database\Manager::connection('session'));
		$this->config = array('table' => 'sessions');
	}

	static function tearDownAfterClass()
	{
		Config::set('database.connections.session', null);
		unlink(STORAGE_PATH.'database/session.sqlite');
	}

}
