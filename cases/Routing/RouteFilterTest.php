<?php use Laravel\Routing\Filter;

class RouteFilterTest extends PHPUnit_Framework_TestCase {

	public function test_simple_filters_can_be_called()
	{
		$filters = array(
			'simple' => function()
			{
				return 'simple';
			},

			'parameters' => function($one, $two, $three = null)
			{
				return $one.'|'.$two.'|'.$three;
			},
		);

		Filter::register($filters);

		$this->assertEquals('simple', Filter::run(array('simple'), array(), true));
		$this->assertEquals('1|2|', Filter::run(array('parameters:1,2'), array(), true));
		$this->assertEquals('3|1|2', Filter::run(array('parameters:1,2'), array(3), true));
	}

	public function test_after_filters_are_called()
	{
		$filters = array(
			'after1' => function()
			{
				define('ROUTE_FILTER_AFTER_1', 1);
			},

			'after2' => function()
			{
				define('ROUTE_FILTER_AFTER_2', 2);
			},
		);

		Filter::register($filters);

		Filter::run(array('after1', 'after2'));

		$this->assertTrue(defined('ROUTE_FILTER_AFTER_1'));
		$this->assertTrue(defined('ROUTE_FILTER_AFTER_2'));
	}

}
