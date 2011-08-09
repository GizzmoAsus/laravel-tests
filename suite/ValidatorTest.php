<?php

class ValidatorTest extends PHPUnit_Framework_TestCase {

	public function testConstructorShouldSetProperties()
	{
		$validator = new Validator($attributes = array('name' => 'test'), $rules = array('name' => 'required'), $messages = array('required' => 'test'));

		$this->assertEquals($validator->attributes, $attributes);
		$this->assertEquals($validator->rules, array('name' => array('required')));
		$this->assertEquals($validator->messages, $messages);
	}

	public function testValidShouldReturnTrueWhenNoErrorsAreSet()
	{
		$this->assertTrue(Validator::make(array('name' => 'test'), array())->valid());
		$this->assertFalse(Validator::make(array('name' => 'test'), array())->invalid());
	}

	/**
	 * @expectedException Exception
	 */
	public function testExceptionThrownIfValidatorDoesntExist()
	{
		Validator::make(array('name' => 'test'), array('name' => 'doesnt-exist'))->valid();
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

	public function testUniqueRulePassesWhenValueIsUnique()
	{
		Utils::setup_db();

		$this->assertTrue(Validator::make(array('email' => 'example@gmail.com'), array('email' => 'unique:users'))->valid());
		$this->assertTrue(Validator::make(array('email' => 'Doesnt-Exist'), array('email' => 'unique:users,name'))->valid());
	}

	public function testUniqueRuleFailsWhenValueIsNotUnique()
	{
		Utils::setup_db();

		$this->assertFalse(Validator::make(array('email' => 'test@example.com'), array('email' => 'unique:users'))->valid());
		$this->assertFalse(Validator::make(array('email' => 'Taylor'), array('email' => 'unique:users,name'))->valid());
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
