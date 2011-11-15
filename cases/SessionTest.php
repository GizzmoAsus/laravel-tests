<?php

use Laravel\IoC;
use Laravel\Config;
use Laravel\Session\Payload as Session;

class SessionTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		Config::load('session');
	}

	public function setUp()
	{
		Config::$items['application']['key'] = 'test_key';
	}

	public function tearDown()
	{
		Config::$items['application']['key'] = '';
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_calls_driver_load_with_session_id($driver)
	{
		$driver->expects($this->once())
                            ->method('load')
                            ->with($this->equalTo('something'));

		$session = new Session($driver, 'something');
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_returns_payload_when_found($driver)
	{
		$this->setDriverExpectation($driver, 'load', $this->getDummyData());

		$session = new Session($driver, 'test');

		$this->assertEquals($this->getDummyData(), $session->session);
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_creates_new_session_when_session_is_null($driver)
	{
		$this->setDriverExpectation($driver, 'load', null);

		$session = new Session($driver, 'test');

		$this->assertInternalType('array', $session->session['data']);
		$this->assertEquals(40, strlen($session->session['id']));
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_creates_new_session_when_session_is_expired($driver)
	{
		$dateTime = new DateTime('1970-01-01');

		$this->setDriverExpectation($driver, 'load', array('last_activity' => $dateTime->getTimestamp()));

		$session = new Session($driver, 'test');

		$this->assertEquals(40, strlen($session->session['id']));
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_session_manager_sets_csrf_token_if_one_is_not_present($driver)
	{
		$session = $this->getDummyData();
		unset($session['data']['csrf_token']);

		$this->setDriverExpectation($driver, 'load', $session);

		$session = new Session($driver, 'test');

		$this->assertArrayHasKey('csrf_token', $session->session['data']);
		$this->assertEquals(40, strlen($session->session['data']['csrf_token']));
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_save_method_calls_driver($driver)
	{
		$driver->expects($this->any())
                            ->method('load')
                            ->will($this->returnValue($this->getDummyData()));

		$session = new Session($driver, 'test');

		$driver->expects($this->once())
                            ->method('save');

		$session->save($driver);
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_save_method_calls_sweep_when_driver_is_sweeper($driver)
	{
		$driver = $this->getMock('SweeperStub', array('sweep'));

		$driver->expects($this->once())->method('sweep');

		$session = new Session($driver, null);

		Config::$items['session']['sweepage'] = array(100, 100);

		$session->save($driver);
	}

	/**
	 * @dataProvider mockProvider
	 */
	public function test_save_method_doesnt_call_sweep_when_driver_isnt_sweeper($driver)
	{
		$driver = $this->getMock('Laravel\\Session\\Drivers\\Driver', array('sweep', 'load', 'save', 'delete'));

		$driver->expects($this->never())->method('sweep');

		$session = new Session($driver, null);

		Config::$items['session']['sweepage'] = array(100, 100);

		$session->save($driver);
	}

	public function test_has_method_indicates_if_item_exists_in_payload()
	{
		$session = $this->getDummySession();

		$this->assertTrue($session->has('name'));
		$this->assertTrue($session->has('age'));
		$this->assertTrue($session->has('gender'));
		$this->assertFalse($session->has('something'));
		$this->assertFalse($session->has('id'));
		$this->assertFalse($session->has('last_activity'));
	}

	public function test_get_method_returns_item_from_payload()
	{
		$session = $this->getDummySession();

		$this->assertEquals('Taylor', $session->get('name'));
		$this->assertEquals(25, $session->get('age'));
		$this->assertEquals('male', $session->get('gender'));
	}

	public function test_get_method_returns_default_when_item_doesnt_exist()
	{
		$session = $this->getDummySession();

		$this->assertNull($session->get('something'));
		$this->assertEquals('Taylor', $session->get('something', 'Taylor'));
		$this->assertEquals('Taylor', $session->get('something', function() {return 'Taylor';}));
	}

	public function test_put_method_adds_to_payload()
	{
		$session = $this->getDummySession();

		$session->put('name', 'Weldon');
		$session->put('workmate', 'Joe');

		$this->assertEquals('Weldon', $session->session['data']['name']);
		$this->assertEquals('Joe', $session->session['data']['workmate']);
	}

	public function test_flash_method_puts_item_in_flash_data()
	{
		$session = $this->getDummySession();
		$session->session = array();

		$session->flash('name', 'Taylor');

		$this->assertEquals('Taylor', $session->session['data'][':new:']['name']);
	}

	public function test_reflash_keeps_all_session_data()
	{
		$session = $this->getDummySession();

		$session->session = array('data' => array(
			':old:' => array(
				'name' => 'Taylor',
				'age' => 25,
			),
			':new:' => array(),
		));

		$session->reflash();

		$this->assertArrayHasKey('name', $session->session['data'][':new:']);
		$this->assertArrayHasKey('age', $session->session['data'][':new:']);
	}

	public function test_keep_method_keeps_specified_session_data()
	{
		$session = $this->getDummySession();

		$session->session = array('data' => array(':old:' => array(
			'name' => 'Taylor',
			'age' => 25,
		)));

		$session->keep('name');

		$this->assertArrayHasKey('name', $session->session['data'][':new:']);
		
		$session->session = array('data' => array(':old:' => array(
			'name' => 'Taylor',
			'age' => 25,
		)));

		$session->keep(array('name', 'age'));

		$this->assertArrayHasKey('name', $session->session['data'][':new:']);
		$this->assertArrayHasKey('age', $session->session['data'][':new:']);
	}

	public function test_flush_method_clears_payload_data()
	{
		$session = $this->getDummySession();
		$session->session = array('data' => array('name' => 'Taylor'));

		$session->flush();

		$this->assertCount(0, $session->session['data']);
	}

	public function test_regenerate_session_sets_new_session_id()
	{
		$session = $this->getDummySession();
		$session->session = array('id' => 'something');

		$session->regenerate();

		$this->assertEquals(40, strlen($session->session['id']));
	}

	public function test_save_method_sets_last_activity_time()
	{
		$session = $this->getDummySession();

		$data = $this->getDummyData();
		unset($data['last_activity']);

		$session->session = $data;
		$session->save($this->getMockDriver());

		$this->assertArrayHasKey('last_activity', $session->session);
	}

	public function test_save_method_ages_all_flash_data()
	{
		$session = $this->getDummySession();

		$session->save($this->getMockDriver());

		$this->assertArrayHasKey('age', $session->session['data'][':old:']);
		$this->assertArrayNotHasKey('gender', $session->session['data'][':old:']);
	}

	// ---------------------------------------------------------------------
	// Support Functions
	// ---------------------------------------------------------------------

	public function getDummySession()
	{
		$session = new Session(new Laravel\Session\Drivers\File(''), null);
		$session->session = $this->getDummyData();
		return $session;
	}

	public function getDummyData()
	{
		return array('id' => 'something', 'last_activity' => time(), 'data' => array(
				'name'        => 'Taylor',
				':new:'       => array('age' => 25),
				':old:'       => array('gender' => 'male'),
				'state'       => 'Oregon',
				'csrf_token'  => 'token',
		));
	}

	// ---------------------------------------------------------------------
	// Providers
	// ---------------------------------------------------------------------

	public function mockProvider()
	{
		return array(array($this->getMockDriver()));
	}

	// ---------------------------------------------------------------------
	// Support Functions
	// ---------------------------------------------------------------------

	private function setDriverExpectation($mock, $method, $session)
	{
		$mock->expects($this->any())
						->method($method)
						->will($this->returnValue($session));
	}

	private function getMockDriver()
	{
		return $this->getMock('Laravel\\Session\\Drivers\\Driver');
	}

	private function getConfig()
	{
		return Config::$items['session'];
	}

}

// ---------------------------------------------------------------------
// Stubs
// ---------------------------------------------------------------------

class SweeperStub implements Laravel\Session\Drivers\Driver, Laravel\Session\Drivers\Sweeper {

	public function load($id) {}
	public function save($session, $config, $exists) {}
	public function delete($id) {}
	public function sweep($expiration) {}

}
