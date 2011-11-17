<?php

abstract class SessionDriverTestCase extends PHPUnit_Framework_TestCase {

	protected $driver = null;
	protected $config = array();

	function testDriverLoadReturnsNullForNewSessions()
	{
		$this->assertNull($this->driver->load(Str::random(40)));
	}

	/**
	 * Test Saving, Loading and Deleting - All in one test as it makes sense
	 */
	function testDriverSavesAndLoadsAndDeletesCorrectly()
	{
		$session = array(
			'id' => Str::random(40),
			'last_activity' => ''.time(),
			'data' => array(
				'name' => 'Phill',
				'email' => 'me@phills.me.uk',
			),
		);

		$this->driver->save($session, $this->config, false);
		$_session = $this->driver->load($session['id']);
		$this->assertEquals($session, $_session);

		$session['data']['gender'] = 'male';
		$session['data']['email']  = 'nospam@phills.me.uk';
		$this->driver->save($session, $this->config, true);
		$_session = $this->driver->load($session['id']);
		$this->assertEquals($session, $_session);

		$this->driver->delete($session['id']);
		$this->assertNull($this->driver->load($session['id']));
	}

}
