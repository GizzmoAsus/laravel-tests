<?php namespace Laravel; use PHPUnit_Framework_TestCase;

class ConfigTest extends PHPUnit_Framework_TestCase {

	public function test_has_method_indicates_if_configuration_item_exists()
	{
		Config::set('hasvalue', true);
		$this->assertTrue(Config::has('hasvalue'));
	}

	public function test_has_method_returns_false_when_item_doesnt_exist()
	{
		$this->assertFalse(Config::has('something'));
	}

	public function test_config_get_can_retrieve_item_from_configuration()
	{
		$this->assertInternalType('array', Config::get('application'));
		$this->assertEquals('http://localhost', Config::get('application.url'));
	}

	public function test_get_method_returns_default_when_requested_item_doesnt_exist()
	{
		$this->assertNull(Config::get('config.item'));
		$this->assertEquals('test', Config::get('config.item', 'test'));
		$this->assertEquals('test', Config::get('config.item', function() {return 'test';}));
	}

	public function test_config_set_can_set_configuration_items()
	{
		Config::set('application.names.test', 'test');
		Config::set('test', array());
		$this->assertEquals('test', Config::get('application.names.test'));
		$this->assertEquals(array(), Config::get('test'));
	}

}
