<?php

class Utils {

	/**
	 * Set the default database connection to the fixture database.
	 *
	 * @return void
	 */
	public static function setup_db()
	{
		$connections = array(
			'sqlite' => array(
				'driver'   => 'sqlite',
				'database' => FIXTURE_PATH.'fixture.sqlite'
			)
		);

		System\Config::set('db.connections', $connections);
	}

}