<?php use Laravel\Arr;

class ArrTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getArray
	 */
	public function test_get_method_returns_item_from_array($array)
	{
		$this->assertEquals($array['email'], Arr::get($array, 'email'));
		$this->assertEquals($array['names']['uncle'], Arr::get($array, 'names.uncle'));
	}

	/**
	 * @dataProvider getArray
	 */
	public function test_get_method_returns_default_when_item_doesnt_exist($array)
	{
		$this->assertNull(Arr::get($array, 'names.aunt'));
		$this->assertEquals('Tammy', Arr::get($array, 'names.aunt', 'Tammy'));
		$this->assertEquals('Tammy', Arr::get($array, 'names.aunt', function() {return 'Tammy';}));
	}

	/**
	 * @dataProvider getArray
	 */
	public function test_set_method_sets_items_in_array($array)
	{
		Arr::set($array, 'name', 'Taylor');
		Arr::set($array, 'names.aunt', 'Tammy');
		Arr::set($array, 'names.friends.best', 'Abigail');
		$this->assertEquals('Taylor', $array['name']);
		$this->assertEquals('Tammy', $array['names']['aunt']);
		$this->assertEquals('Abigail', $array['names']['friends']['best']);
	}

	/**
	 * @dataProvider getArray
	 */
	public function test_first_method_returns_first_item_passing_truth_test($array)
	{
		$array['email2'] = 'taylor@hotmail.com';
		$this->assertEquals('taylorotwell@gmail.com', Arr::first($array, function($k, $v) {return substr($v, 0, 3) == 'tay';}));
	}

	/**
	 * @dataProvider getArray
	 */
	public function test_first_method_returns_default_when_no_matching_item_is_found($array)
	{
		$this->assertNull(Arr::first($array, function($k, $v) {return $v === 'something';}));
		$this->assertEquals('default', Arr::first($array, function($k, $v) {return $v === 'something';}, 'default'));
		$this->assertEquals('default', Arr::first($array, function($k, $v) {return $v === 'something';}, function() {return 'default';}));
	}

	public function getArray()
	{
		return array(array(
			array(
				'email' => 'taylorotwell@gmail.com',
				'names' => array(
					'uncle' => 'Mike',
				),
			)
		));
	}

}
