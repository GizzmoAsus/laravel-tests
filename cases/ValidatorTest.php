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

	public function testValidShouldReturnTrueWhenNoErrorsAreSet()
	{
		$this->assertTrue(Validator::make(array('name' => 'test'), array())->valid());
		$this->assertFalse(Validator::make(array('name' => 'test'), array())->invalid());
	}

	/**
	 * @dataProvider passingRuleProvider
	 */
	public function testRulesPassWhenProperCriteriaIsMet($attributes, $rules)
	{
		$this->assertTrue(Validator::make($attributes, $rules)->valid());
	}

	/**
	 * @dataProvider failingRuleProvider
	 */
	public function testRulesFailWhenProperCriteriaIsNotMet($attributes, $rules)
	{
		$this->assertFalse(Validator::make($attributes, $rules)->valid());
	}

	public function testMimePassesWhenMimeMatchesFileContent()
	{
		$this->assertTrue(Validator::make(array('picture' => array('tmp_name' => FIXTURE_PATH.'gravatar.jpg')), array('picture' => 'mimes:jpg'))->valid());
		$this->assertTrue(Validator::make(array('picture' => array('tmp_name' => FIXTURE_PATH.'gravatar.jpg')), array('picture' => 'image'))->valid());
	}

	public function testMimeFailsWhenMimeDoesntMatchContent()
	{
		$this->assertFalse(Validator::make(array('picture' => array('tmp_name' => FIXTURE_PATH.'fixture.sqlite')), array('picture' => 'mimes:jpg'))->valid());
		$this->assertFalse(Validator::make(array('picture' => array('tmp_name' => FIXTURE_PATH.'fixture.sqlite')), array('picture' => 'image'))->valid());
	}

	public function testCustomMessagesAreRespected()
	{
		$validator = Validator::make(array('name' => ''), array('name' => 'required'), array('required' => 'Test Message'));
		$validator->valid();

		$this->assertEquals($validator->errors->first('name'), 'Test Message');

		$validator = Validator::make(array('name' => ''), array('name' => 'required'), array('name_required' => 'Test Message'));
		$validator->valid();

		$this->assertEquals($validator->errors->first('name'), 'Test Message');
	}

	public function testErrorMessagePlaceholdersAreReplaced()
	{
		$messages = array('required' => ':attribute', 'size' => ':size', 'between' => ':max:min', 'min' => ':min', 'max' => ':max', 'in' => ':values');

		$validator = Validator::make(array('name' => '', 'first_name' => ''), array('name' => 'required', 'first_name' => 'required'), $messages);
		$validator->valid();

		$this->assertEquals($validator->errors->first('name'), 'name');
		$this->assertEquals($validator->errors->first('first_name'), 'first name');

		$validator = Validator::make(array('name' => 'taylor'), array('name' => 'size:3'), $messages);
		$validator->valid();

		$this->assertEquals($validator->errors->first('name'), '3');

		$validator = Validator::make(array('name' => 'taylor'), array('name' => 'max:3'), $messages);
		$validator->valid();

		$this->assertEquals($validator->errors->first('name'), '3');

		$validator = Validator::make(array('name' => 'taylor'), array('name' => 'min:7'), $messages);
		$validator->valid();

		$this->assertEquals($validator->errors->first('name'), '7');

		$validator = Validator::make(array('name' => 'taylor'), array('name' => 'between:1,2'), $messages);
		$validator->valid();

		$this->assertEquals($validator->errors->first('name'), '21');

		$validator = Validator::make(array('name' => 'taylor'), array('name' => 'in:1,2'), $messages);
		$validator->valid();

		$this->assertEquals($validator->errors->first('name'), '1, 2');
	}

	public function passingRuleProvider()
	{
		return array(
			array(array('test' => 'test'), array('test' => 'required')),
			array(array('test' => 'test', 'test_confirmation' => 'test'), array('test' => 'confirmed')),
			array(array('test' => 'yes'), array('test' => 'accepted')),
			array(array('test' => '1'), array('test' => 'accepted')),
			array(array('test' => 1), array('test' => 'numeric')),
			array(array('test' => 1), array('test' => 'integer')),
			array(array('test' => 3), array('test' => 'numeric|size:3')),
			array(array('test' => 'aaa'), array('test' => 'size:3')),
			array(array('test' => 'aaaaa'), array('test' => 'between:5,10')),
			array(array('test' => 'aaaaaaaaaa'), array('test' => 'between:5,10')),
			array(array('test' => 'aaaaaa'), array('test' => 'between:5,10')),
			array(array('test' => 5), array('test' => 'numeric|between:5,10')),
			array(array('test' => 10), array('test' => 'numeric|between:5,10')),
			array(array('test' => 7), array('test' => 'numeric|between:5,10')),
			array(array('test' => 3), array('test' => 'numeric|min:3')),
			array(array('test' => 4), array('test' => 'numeric|min:3')),
			array(array('test' => '123'), array('test' => 'min:3')),
			array(array('test' => '1234'), array('test' => 'min:3')),
			array(array('test' => 3), array('test' => 'numeric|max:3')),
			array(array('test' => 2), array('test' => 'numeric|max:3')),
			array(array('test' => 'aaa'), array('test' => 'max:3')),
			array(array('test' => 'aa'), array('test' => 'max:3')),
			array(array('test' => 'name'), array('test' => 'in:name,other')),
			array(array('test' => 'name'), array('test' => 'in:other,name')),
			array(array('test' => 'name'), array('test' => 'not_in:test,other')),
			array(array('test' => 'test@example.com'), array('test' => 'email')),
			array(array('test' => 'http://www.google.com'), array('test' => 'url')),
			array(array('test' => 'http://www.google.com'), array('test' => 'active_url')),
			array(array('test' => 'abc'), array('test' => 'alpha')),
			array(array('test' => 'abc123'), array('test' => 'alpha_num')),
			array(array('test' => 'abc'), array('test' => 'alpha_num')),
			array(array('test' => 'abc123'), array('test' => 'alpha_dash')),
			array(array('test' => 'abc123_-'), array('test' => 'alpha_dash')),
			array(array('test' => 'abc'), array('test' => 'alpha_dash')),
		);
	}

	public function failingRuleProvider()
	{
		return array(
			array(array('test' => ''), array('test' => 'required')),
			array(array(), array('test' => 'required')),
			array(array('test' => 'test', 'test_confirmation' => 'test_doesnt_match'), array('test' => 'confirmed')),
			array(array('test' => 'test'), array('test' => 'confirmed')),
			array(array('test' => 'no'), array('test' => 'accepted')),
			array(array('test' => 'a1'), array('test' => 'numeric')),
			array(array('test' => 1.2), array('test' => 'integer')),
			array(array('test' => 'a1'), array('test' => 'integer')),
			array(array('test' => 4), array('test' => 'numeric|size:3')),
			array(array('test' => 'aaaa'), array('test' => 'size:3')),
			array(array('test' => 'aaaa'), array('test' => 'between:5,10')),
			array(array('test' => 'aaaaaaaaaaa'), array('test' => 'between:5,10')),
			array(array('test' => 4), array('test' => 'numeric|between:5,10')),
			array(array('test' => 11), array('test' => 'numeric|between:5,10')),
			array(array('test' => 2), array('test' => 'numeric|min:3')),
			array(array('test' => 'aa'), array('test' => 'min:3')),
			array(array('test' => 4), array('test' => 'numeric|max:3')),
			array(array('test' => 'aaaa'), array('test' => 'max:3')),
			array(array('test' => 'test'), array('test' => 'in:name,other')),
			array(array('test' => 'test'), array('test' => 'in:other,name')),
			array(array('test' => 'other'), array('test' => 'not_in:test,other')),
			array(array('test' => 'test'), array('test' => 'email')),
			array(array('test' => 'not-a-url'), array('test' => 'url')),
			array(array('test' => 'http://www.iewc.dslsks.com'), array('test' => 'active_url')),
			array(array('test' => 'abc1'), array('test' => 'alpha')),
			array(array('test' => 'abc-123'), array('test' => 'alpha_num')),
			array(array('test' => 'abc.123'), array('test' => 'alpha_dash')),
		);
	}

}
