<?php

use Laravel\Lang;
use Laravel\Validator;

class ValidatorTest extends PHPUnit_Framework_TestCase {

	public function test_simple_group_of_validations()
	{
		$rules = array(
			'email'    => 'required|email',
			'password' => 'required|confirmed|min:6',
			'name'     => 'required|alpha',
			'age'      => 'required',
		);

		$attributes = array(
			'email'                 => 'taylorotwell',
			'password'              => 'something',
			'password_confirmation' => 'something',
			'name'                  => 'taylor5',
		);

		$messages = array('name_alpha' => 'The name must be alphabetic!');

		$validator = Validator::make($attributes, $rules, $messages);

		$this->assertFalse($validator->valid());
		$this->assertTrue($validator->errors->has('name'));
		$this->assertTrue($validator->errors->has('email'));
		$this->assertFalse($validator->errors->has('password'));
		$this->assertCount(1, $validator->errors->get('name'));
		$this->assertEquals('The name must be alphabetic!', $validator->errors->first('name'));
		$this->assertEquals(Lang::line('validation.email', array('attribute' => 'email'))->get(), $validator->errors->first('email'));
		$this->assertEquals(Lang::line('validation.required', array('attribute' => 'age'))->get(), $validator->errors->first('age'));
	}

	public function test_size_rules_return_correct_error_message()
	{
		$_FILES['validator_photo']['size'] = 10000;

		$rules = array(
			'name'            => 'required|min:6',
			'age'             => 'required|integer|between:10,20',
			'validator_photo' => 'required|min:100',
		);

		$validator = Validator::make(array('name' => 'tay', 'age' => 25) + $_FILES, $rules);

		$this->assertFalse($validator->valid());

		$language = require LANG_PATH.'en/validation.php';

		$name = str_replace(array(':attribute', ':min'), array('name', '6'), $language['min']['string']);
		$this->assertEquals($name, $validator->errors->first('name'));

		$age = str_replace(array(':attribute', ':min', ':max'), array('age', '10', '20'), $language['between']['numeric']);
		$this->assertEquals($age, $validator->errors->first('age'));

		$name = str_replace(array(':attribute', ':min'), array('validator photo', '100'), $language['min']['file']);
		$this->assertEquals($name, $validator->errors->first('validator_photo'));
	}

}
