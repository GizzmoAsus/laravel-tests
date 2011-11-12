<?php

use Laravel\Routing\Filter;
use Laravel\Routing\Controller;

class ControllerTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		require_once SYS_PATH.'routing/filter'.EXT;
	}

	public function test_basic_call_can_execute_controller_method()
	{
		$response = Controller::call('blog@index');
		$this->assertEquals('blog@index', $response->content);
	}

	public function test_controller_can_resolve_controller_instances()
	{
		$this->assertInstanceOf('Laravel\\Routing\\Controller', Controller::resolve('blog'));
		$this->assertInstanceOf('Laravel\\Routing\\Controller', Controller::resolve('filter.basic'));
	}

	public function test_calling_undefined_method_returns_404()
	{
		$this->assertEquals(Controller::call('blog@failure')->status, 404);
	}

	public function test_before_filters_interrupt_controller_method()
	{
		Filter::register(array('controller_before_1' => function()
		{
			return 'Filtered!';			
		}));

		$controller = Controller::resolve('filter.basic');

		$this->assertEquals($controller->execute('before')->content, 'Filtered!');
	}

	public function test_after_filters_on_controller_are_called()
	{
		Filter::register(array('controller_after_1' => function()
		{
			define('CONTROLLER_AFTER_1', 1);
		}));

		$controller = Controller::resolve('filter.basic');

		$controller->execute('after');

		$this->assertTrue(defined('CONTROLLER_AFTER_1'));
	}

	public function test_filters_can_be_included()
	{
		Filter::register(array('controller_after_2' => function()
		{
			define('CONTROLLER_AFTER_2', 1);
		}));

		$controller = Controller::resolve('filter.included');

		$controller->execute('passive');

		$this->assertFalse(defined('CONTROLLER_AFTER_2'));

		$controller->execute('active');

		$this->assertTrue(defined('CONTROLLER_AFTER_2'));
	}

	public function test_filters_can_be_excluded()
	{
		Filter::register(array('controller_after_3' => function()
		{
			define('CONTROLLER_AFTER_3', 1);
		}));

		$controller = Controller::resolve('filter.excluded');

		$controller->execute('passive');
		$controller->execute('passive_2');

		$this->assertFalse(defined('CONTROLLER_AFTER_3'));

		$controller->execute('active');

		$this->assertTrue(defined('CONTROLLER_AFTER_3'));
	}

	public function test_parameters_are_passed_to_controller_method()
	{
		$this->assertEquals(Controller::call('blog@post', array(25))->content, 'post|25');
	}

	public function test_resolve_method_can_resolve_controller_out_of_ioc_container()
	{
		Laravel\IoC::register('controllers.ioc', function()
		{
			return new Ioc_Controller;
		});

		$this->assertTrue(Controller::resolve('ioc') instanceof Ioc_Controller);
	}

	public function test_dynamically_accessing_properties_retrieves_from_ioc_container()
	{
		Laravel\IoC::register('mailer', function() {return 'SwiftMailer';});

		$this->assertEquals(Controller::resolve('blog')->mailer, 'SwiftMailer');
	}

	public function test_call_to_non_existent_controller_returns_404()
	{
		$this->assertEquals(Controller::call('doesnt@exist')->status, 404);
	}

}