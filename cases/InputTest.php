<?php use Laravel\Input;

class InputTest extends PHPUnit_Framework_TestCase {

	protected $input = array(
		'name' => 'Taylor',
		'age'  => 25,
	);

	public function setUp()
	{
		Input::$input = $this->input;
	}

	public function tearDown()
	{
		$_FILES = array();
	}

	public function test_get_method_returns_input_if_it_exists()
	{
		$this->assertEquals('Taylor', Input::get('name'));
		$this->assertEquals(25, Input::get('age'));
	}

	public function test_get_method_returns_default_when_input_doesnt_exist()
	{
		$this->assertNull(Input::get('something'));
		$this->assertEquals('Fred', Input::get('friend', 'Fred'));
		$this->assertEquals('Fred', Input::get('friend', function() {return 'Fred';}));
	}

	public function test_has_method_returns_true_if_item_exists_in_input()
	{
		$this->assertTrue(Input::has('name'));
		$this->assertTrue(Input::has('age'));
	}

	public function test_has_method_returns_false_if_item_doesnt_exist()
	{
		$this->assertFalse(Input::has('something'));
	}

	public function test_all_methods_returns_all_of_the_input()
	{
		$_FILES['photo'] = 'test';
		$input = array_merge($this->input, $_FILES);
		$this->assertEquals($input, Input::all());
	}

	public function test_file_method_returns_from_file_array()
	{
		$_FILES['photo'] = array('name' => 'test', 'nest' => array('value' => 'test'));
		$this->assertEquals('test', Input::file('photo.name'));
		$this->assertEquals('test', Input::file('photo.nest.value'));
	}

	public function test_file_method_returns_default_if_file_doesnt_exist()
	{
		$this->assertNull(Input::file('something'));
		$this->assertEquals('Fred', Input::file('something', 'Fred'));
		$this->assertEquals('Fred', Input::file('something', function() {return 'Fred';}));
	}

}