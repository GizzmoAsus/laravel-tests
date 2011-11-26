<?php
/**
 * FormTest.php
 *
 * This file is the main test file for the Form Builder Class
 *
 * @package Laravel-Tests
 * @subpackage Core
 */

use Laravel\IoC;
use Laravel\Config;
use Laravel\Session\Payload as Session;
use Laravel\Form;

/**
 * FormTest
 *
 * A test class for the main form builder available within Laravel
 *
 * @author Matthew Kellett <email@matthewkellett.co.uk>
 *
 */
class FormTest extends PHPUnit_Framework_TestCase {
	/**
	 * FormTest::set_up
	 *
	 * A function to set up the testing environments before every test
	 *
	 * @access protected
	 * @return void
	 */
	protected function setUp() {
		$_SERVER['REQUEST_URI'] = "http://localhost";
	}

	/**
	 * FormTest::tear_down
	 *
	 * A function to undo actions after every test is complete
	 *
	 * @access protected
	 * @return void
	 */
	protected function tearDown() {
		unset($_SERVER['REQUEST_URI']);
	}

	/**
	 * FormTest::provider_method
	 *
	 * A data provider for the test_method method
	 *
	 * @return array An array of params for the test method
	 */
	public function provider_method() {
		return array(
			array('POST', ''),
			array('POST', null),
			array('POST', 'post'),
			array('POST', 'PUT'),
			array('POST', 'DELETE'),
			array('GET', 'get'),
			array('POST', 'randomg string'),
			array('POST', new stdClass()),
		);
	}

	/**
	 * FormTest::test_method()
	 *
	 * A test method for the open tag of a form
	 *
	 * @see Laravel/Form
	 * @dataProvider provider_method
	 * @param string $expected The expected response from the method call
	 * @param string $method The method for submitting the form
	 * @access public
	 * @return void
	 */
	public function test_method($expected, $method) {
		$reflected = self::get_method('method');
		$form = new Form();
		$form_method = $reflected->invokeArgs($form, array($method));

		$this->assertSame($expected, $form_method);
	}

	/**
	 * FormTest::provider_method
	 *
	 * A data provider for the test_method method
	 *
	 * @return array An array of params for the test method action($action, $https)
	 */
	public function provider_action() {
		return array(
			array('http://localhost/index.php/', '', false),
			array('https://localhost/index.php/', '', true),
			array('http://localhost/index.php/', null, false),
			array('https://localhost/index.php/', null, true),
			array('http://localhost/index.php/user', 'user', false),
			array('https://localhost/index.php/user', 'user', true),
			array('http://localhost/index.php/user/login', 'user/login', false),
			array('https://localhost/index.php/user/login', 'user/login', true)
		);
	}

	/**
	 * FormTest::test_action()
	 *
	 * A test method for the open tag of a form
	 *
	 * @see Laravel/Form
	 * @dataProvider provider_action
	 * @param string $expected The expected response from the method call
	 * @param array $options An array containing the options for the open method
	 * @param boolean $secure Whether the URL should be https or not
	 * @access public
	 * @return void
	 */
	public function test_action($expected, $method, $secure) {
		$reflected = self::get_method('action');
		$form = new Form();
		$form_method = $reflected->invokeArgs($form, array($method, $secure));

		$this->assertSame($expected, $form_method);
	}

	/**
	 * FormTest::provider_open
	 *
	 * A data provider for the test_open method
	 *
	 * @return array An array of params for the test method
	 */
	public function provider_open() {
		return array(
			array(
				'<form method="POST" action="http://localhost/index.php/" accept-charset="UTF-8">'.PHP_EOL,
				array(
					'action' 		=> '',
					'method' 		=> '',
					'attributes' 	=> '',
					'https' 		=> '',
				)
			),
			array(
				'<form method="POST" action="http://localhost/index.php/" accept-charset="UTF-8">'.PHP_EOL,
				array(
					'action' 		=> null,
					'method' 		=> null,
					'attributes' 	=> null,
					'https' 		=> null,
				)
			),
			array(
				'<form method="POST" action="http://localhost/index.php/user/login" accept-charset="UTF-8">'.PHP_EOL,
				array(
					'action' 		=> '/user/login',
					'method' 		=> '',
					'attributes' 	=> array(),
					'https' 		=> '',
				)
			),
			array(
				'<form method="POST" action="https://localhost/index.php/user/login" accept-charset="UTF-8">'.PHP_EOL,
				array(
					'action' 		=> '/user/login',
					'method' 		=> '',
					'attributes' 	=> array(),
					'https' 		=> true,
				)
			),
			array(
				'<form id="test1" method="POST" action="http://localhost/index.php/user/login" accept-charset="UTF-8">'.PHP_EOL,
				array(
					'action' 		=> '/user/login',
					'method' 		=> '',
					'attributes' 	=> array('id' => 'test1'),
					'https' 		=> '',
				)
			),
			array(
				'<form id="test1" method="POST" action="https://localhost/index.php/user/login" accept-charset="UTF-8">'.PHP_EOL,
				array(
					'action' 		=> '/user/login',
					'method' 		=> '',
					'attributes' 	=> array('id' => 'test1'),
					'https' 		=> true,
				)
			),
			array(
				'<form class="test2" method="POST" action="http://localhost/index.php/user/login" accept-charset="UTF-8">'.PHP_EOL,
				array(
					'action' 		=> '/user/login',
					'method' 		=> '',
					'attributes' 	=> array('class' => 'test2'),
					'https' 		=> '',
				)
			),
		);
	}

	/**
	 * FormTest::test_open()
	 *
	 * A test method for the opening tag of a form
	 *
	 * @see Laravel/Form::method
	 * @depends test_method
	 * @depends test_action
	 * @dataProvider provider_open
	 * @param string $expected The opening form Tag that should be returned
	 * @param array $options An array containing the options for the open method
	 * @access public
	 * @return void
	 */
	public function test_open($expected, $options) {
		foreach ($options as $key => $value) {
			$$key = $value;
		}

		$this->assertEquals($expected, Form::open($action, $method, $attributes, $https));
	}

	/**
	 * FormTest::test_open_secure()
	 *
	 * A test method for the secure opening tag of a form
	 *
	 * @see Laravel/Form::open_secure
	 * @depends test_open
	 * @dataProvider provider_open
	 * @param string $expected The opening form Tag that should be returned
	 * @param array $options An array containing the options for the open method
	 * @access public
	 * @return void
	 */
	public function test_open_secure($expected, $options) {
		foreach ($options as $key => $value) {
			$$key = $value;
		}

		$expected = str_replace(array('http', 'httpss'), 'https', $expected);
		$this->assertEquals($expected, Form::open_secure($action, $method, $attributes));
	}

	/**
	 * FormTest::test_open_for_files()
	 *
	 * A test method for the opening tag of a form that allows file uploads
	 *
	 * @see Laravel/Form::open_for_files
	 * @depends test_open
	 * @dataProvider provider_open
	 * @param string $expected The opening form Tag that should be returned
	 * @param array $options An array containing the options for the open method
	 * @access public
	 * @return void
	 */
	public function test_open_files($expected, $options) {
		foreach ($options as $key => $value) {
			$$key = $value;
		}

		$expected = str_replace('method="POST"', 'enctype="multipart/form-data" method="POST"', $expected);
		$this->assertEquals($expected, Form::open_for_files($action, $method, $attributes, $https));
	}

	/**
	 * FormTest::test_open_secure_files()
	 *
	 * A test method for the secure opening tag of a form that allows file uploads
	 *
	 * @see Laravel/Form::open_secure_for_files
	 * @depends test_open
	 * @dataProvider provider_open
	 * @param string $expected The opening form Tag that should be returned
	 * @param array $options An array containing the options for the open method
	 * @access public
	 * @return void
	 */
	public function test_open_secure_files($expected, $options) {
		foreach ($options as $key => $value) {
			$$key = $value;
		}

		$expected = str_replace(array('http', 'httpss'), 'https', $expected);
		$expected = str_replace('method="POST"', 'enctype="multipart/form-data" method="POST"', $expected);
		$this->assertEquals($expected, Form::open_secure_for_files($action, $method, $attributes, $https));
	}

	/**
	 * FormTest::test_label()
	 *
	 * A test method for the creating labels
	 *
	 * @see Laravel/Form::label
	 * @access public
	 * @return void
	 */
	public function test_label() {
		$label1 = '<label for=""></label>'.PHP_EOL;
		$this->assertEquals($label1, Form::label('', '', array()));

		$label2 = '<label for=""></label>'.PHP_EOL;
		$this->assertEquals($label2, Form::label(null, null, array()));

		$label3 = '<label for="test_name">Test Value</label>'.PHP_EOL;
		$this->assertEquals($label3, Form::label('test_name', 'Test Value', array()));

		$label4 = '<label for="test_name" class="test_class">Test Value</label>'.PHP_EOL;
		$this->assertEquals($label4, Form::label('test_name', 'Test Value', array('class' => 'test_class')));
	}

	/**
	 * FormTest::test_input()
	 *
	 * A test method for the input creation tag
	 *
	 * @see Laravel/Form::input
	 * @access public
	 * @return void
	 */
	public function test_input() {
		$input1 = '<input type="" name="" value="" id="">'.PHP_EOL;
		$this->assertEquals($input1, Form::input('', '', '', array()));

		$input2 = '<input>'.PHP_EOL;
		$this->assertEquals($input2, Form::input(null, null, null, array()));

		$input3 = '<input type="text" name="test_text" value="Test Value">'.PHP_EOL;
		$this->assertEquals($input3, Form::input('text', 'test_text', 'Test Value', array()));

		$input4 = '<input class="inp_txt" type="text" name="test_text" value="Test Value">'.PHP_EOL;
		$this->assertEquals($input4, Form::input('text', 'test_text', 'Test Value', array('class' => 'inp_txt')));

		$input5 = '<input type="hidden" name="test_hidden" value="Test Hidden">'.PHP_EOL;
		$this->assertEquals($input5, Form::input('hidden', 'test_hidden', 'Test Hidden', array()));

		$input5 = '<input type="password" name="user_pass" value="">'.PHP_EOL;
		$this->assertEquals($input5, Form::input('password', 'user_pass', '', array()));
	}

	/**
	 * FormTest::test_text()
	 *
	 * A test method for the text input convenience method
	 *
	 * @see Laravel/Form::text
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_text() {
		$input1 = '<input type="text" name="test_text">'.PHP_EOL;
		$this->assertEquals($input1, Form::text('test_text'));

		$input2 = '<input type="text" name="test_text" value="Test Value">'.PHP_EOL;
		$this->assertEquals($input2, Form::text('test_text', 'Test Value', array()));

		$input3 = '<input class="inp_txt" type="text" name="test_text" value="Test Value">'.PHP_EOL;
		$this->assertEquals($input3, Form::text('test_text', 'Test Value', array('class' => 'inp_txt')));
	}

	/**
	 * FormTest::test_password()
	 *
	 * A test method for the password input convenience method
	 *
	 * @see Laravel/Form::password
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_password() {
		$input1 = '<input type="password" name="test_password">'.PHP_EOL;
		$this->assertEquals($input1, Form::password('test_password'));

		$input2 = '<input type="password" name="test_password">'.PHP_EOL;
		$this->assertEquals($input2, Form::password('test_password', array()));

		$input3 = '<input class="inp_txt" type="password" name="test_password">'.PHP_EOL;
		$this->assertEquals($input3, Form::password('test_password', array('class' => 'inp_txt')));

		$input4 = '<input class="inp_txt" type="password" name="test_password">'.PHP_EOL;
		$this->assertEquals($input4, Form::password('test_password', array('class' => 'inp_txt', 'value' => 'Test Value')));
	}

	/**
	 * FormTest::test_hidden()
	 *
	 * A test method for the hidden input convenience method
	 *
	 * @see Laravel/Form::hidden
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_hidden() {
		$input1 = '<input type="hidden" name="test_hidden">'.PHP_EOL;
		$this->assertEquals($input1, Form::hidden('test_hidden'));

		$input2 = '<input type="hidden" name="test_hidden" value="Test Value">'.PHP_EOL;
		$this->assertEquals($input2, Form::hidden('test_hidden', 'Test Value', array()));

		$input3 = '<input id="inp_txt" type="hidden" name="test_hidden" value="Test Value">'.PHP_EOL;
		$input3 = '<input id="inp_txt" type="hidden" name="test_hidden" value="Test Value">'.PHP_EOL;
		$this->assertEquals($input3, Form::hidden('test_hidden', 'Test Value', array('id' => 'inp_txt')));
	}

	/**
	 * FormTest::test_search()
	 *
	 * A test method for the search input convenience method
	 *
	 * @see Laravel/Form::search
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_search() {
		$input1 = '<input type="search" name="test_search">'.PHP_EOL;
		$this->assertEquals($input1, Form::search('test_search'));

		$input2 = '<input type="search" name="test_search" value="Test Value">'.PHP_EOL;
		$this->assertEquals($input2, Form::search('test_search', 'Test Value', array()));

		$input3 = '<input class="inp_search" type="search" name="test_search" value="Test Value">'.PHP_EOL;
		$this->assertEquals($input3, Form::search('test_search', 'Test Value', array('class' => 'inp_search')));
	}

	/**
	 * FormTest::test_email()
	 *
	 * A test method for the email input convenience method
	 *
	 * @see Laravel/Form::email
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_email() {
		$input1 = '<input type="email" name="test_email">'.PHP_EOL;
		$this->assertEquals($input1, Form::email('test_email'));

		$input2 = '<input type="email" name="test_email" value="test@testemail.com">'.PHP_EOL;
		$this->assertEquals($input2, Form::email('test_email', 'test@testemail.com', array()));

		$input3 = '<input class="inp_email" type="email" name="test_email" value="test@testemail.com">'.PHP_EOL;
		$this->assertEquals($input3, Form::email('test_email', 'test@testemail.com', array('class' => 'inp_email')));
	}

	/**
	 * FormTest::test_telephone()
	 *
	 * A test method for the telephone input convenience method
	 *
	 * @see Laravel/Form::telephone
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_telephone() {
		$input1 = '<input type="tel" name="test_tel">'.PHP_EOL;
		$this->assertEquals($input1, Form::telephone('test_tel'));

		$input2 = '<input type="tel" name="test_tel" value="01234567890">'.PHP_EOL;
		$this->assertEquals($input2, Form::telephone('test_tel', '01234567890', array()));

		$input3 = '<input class="inp_tel" type="tel" name="test_tel" value="+441234567890">'.PHP_EOL;
		$this->assertEquals($input3, Form::telephone('test_tel', '+441234567890', array('class' => 'inp_tel')));
	}

	/**
	 * FormTest::test_url()
	 *
	 * A test method for the url input convenience method
	 *
	 * @see Laravel/Form::url
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_url() {
		$input1 = '<input type="url" name="test_url">'.PHP_EOL;
		$this->assertEquals($input1, Form::url('test_url'));

		$input2 = '<input type="url" name="test_url" value="http://www.laravel.com">'.PHP_EOL;
		$this->assertEquals($input2, Form::url('test_url', 'http://www.laravel.com', array()));

		$input3 = '<input class="inp_url" type="url" name="test_url" value="http://laravel.com">'.PHP_EOL;
		$this->assertEquals($input3, Form::url('test_url', 'http://laravel.com', array('class' => 'inp_url')));
	}

	/**
	 * FormTest::test_number()
	 *
	 * A test method for the number input convenience method
	 *
	 * @see Laravel/Form::number
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_number() {
		$input1 = '<input type="number" name="test_number">'.PHP_EOL;
		$this->assertEquals($input1, Form::number('test_number'));

		$input2 = '<input type="number" name="test_number" value="0123456">'.PHP_EOL;
		$this->assertEquals($input2, Form::number('test_number', '0123456', array()));

		$input3 = '<input class="inp_num" type="number" name="test_number" value="123456">'.PHP_EOL;
		$this->assertEquals($input3, Form::number('test_number', '123456', array('class' => 'inp_num')));
	}

	/**
	 * FormTest::test_file()
	 *
	 * A test method for the file input convenience method
	 *
	 * @see Laravel/Form::file
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_file() {
		$input1 = '<input type="file" name="test_file">'.PHP_EOL;
		$this->assertEquals($input1, Form::file('test_file'));

		$input2 = '<input type="file" name="test_file">'.PHP_EOL;
		$this->assertEquals($input2, Form::file('test_file', array()));

		$input2 = '<input class="inp_file" type="file" name="test_file">'.PHP_EOL;
		$this->assertEquals($input2, Form::file('test_file', array('class' => 'inp_file')));
	}

	/**
	 * FormTest::test_textarea()
	 *
	 * A test method for the textarea convenience method
	 *
	 * @see Laravel/Form::textarea
	 * @access public
	 * @return void
	 */
	public function test_textarea() {
		$textarea1 = '<textarea name="test_textarea" rows="10" cols="50"></textarea>'.PHP_EOL;
		$this->assertEquals($textarea1, Form::textarea('test_textarea'));

		$textarea2 = '<textarea name="test_textarea" rows="10" cols="50">Text Area Test Content</textarea>'.PHP_EOL;
		$this->assertEquals($textarea2, Form::textarea('test_textarea', 'Text Area Test Content', array()));

		$textarea3 = '<textarea class="txt_area" rows="20" cols="75" name="test_textarea">Text Area Test Content</textarea>'.PHP_EOL;
		$this->assertEquals(
			$textarea3,
			Form::textarea('test_textarea', 'Text Area Test Content', array('class' => 'txt_area', 'rows' => 20, 'cols' => 75))
		);
	}

	/**
	 * FormTest::test_option()
	 *
	 * A test method for the select option convenience method
	 *
	 * @see Laravel/Form::option
	 * @access public
	 * @return void
	 */
	public function test_option() {
		$reflected = self::get_method('option');
		$form = new Form();

		$input1 = '<option value="test_opt">Test Option</option>';
		$this->assertEquals($input1, $reflected->invokeArgs($form, array('test_opt', 'Test Option', false)));

		$input2 = '<option value="test_opt">Test Option</option>';
		$this->assertEquals($input2, $reflected->invokeArgs($form, array('test_opt', 'Test Option', false)));

		$input2 = '<option value="test_opt" selected="selected">Test Option</option>';
		$this->assertEquals($input2, $reflected->invokeArgs($form, array('test_opt', 'Test Option', true)));
	}

	/**
	 * FormTest::test_select()
	 *
	 * A test method for the select convenience method
	 *
	 * @see Laravel/Form::select
	 * @depends test_option
	 * @access public
	 * @return void
	 */
	public function test_select() {
		$options = array(
			'test1'	=> 'test value 1',
			'test2'	=> 'test value 2',
			'test3'	=> 'test value 3'
		);

		$textarea1 = '<select name="test_sel"></select>'.PHP_EOL;
		$this->assertEquals($textarea1, Form::select('test_sel'));

		$textarea2 = '<select name="test_sel">';
		foreach ($options as $key => $value) {
			$textarea2 .= '<option value="'.$key.'">'.$value.'</option>';
		}
		$textarea2 .= '</select>'.PHP_EOL;
		$this->assertEquals($textarea2, Form::select('test_sel', $options, null, array()));

		$textarea3 = '<select class="inp_sel" name="test_sel">';
		foreach ($options as $key => $value) {
			$textarea3 .= '<option value="'.$key.'"'.($key == 'test2' ? ' selected="selected"' : '').'>'.$value.'</option>';
		}
		$textarea3 .= '</select>'.PHP_EOL;
		$this->assertEquals(
			$textarea3,
			Form::select('test_sel', $options, 'test2', array('class' => 'inp_sel'))
		);
	}

	/**
	 * FormTest::test_checkable()
	 *
	 * A test method for the checkable convenience method
	 *
	 * @see Laravel/Form::checkable
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_checkable() {
		$reflected = self::get_method('checkable');
		$form = new Form();

		$check1 = '<input type="checkbox" name="chkbx" value="Test Check 1">'.PHP_EOL;
		$this->assertEquals($check1, $reflected->invokeArgs($form, array('checkbox', 'chkbx', 'Test Check 1', false, array())));

		$check2 = '<input checked="checked" type="checkbox" name="chkbx" value="Test Check 2">'.PHP_EOL;
		$this->assertEquals($check2, $reflected->invokeArgs($form, array('checkbox', 'chkbx', 'Test Check 2', true, array())));

		$check3 = '<input class="chkbox" checked="checked" type="checkbox" name="chkbx" value="Test Check 3">'.PHP_EOL;
		$this->assertEquals($check3, $reflected->invokeArgs($form, array('checkbox', 'chkbx', 'Test Check 3', true, array('class' => 'chkbox'))));

		$radio1 = '<input type="radio" name="radbtn" value="Test Radio 1">'.PHP_EOL;
		$this->assertEquals($radio1, $reflected->invokeArgs($form, array('radio', 'radbtn', 'Test Radio 1', false, array())));

		$radio2 = '<input checked="checked" type="radio" name="radbtn" value="Test Radio 2">'.PHP_EOL;
		$this->assertEquals($radio2, $reflected->invokeArgs($form, array('radio', 'radbtn', 'Test Radio 2', true, array())));

		$radio3 = '<input class="radbtn" checked="checked" type="radio" name="radbtn" value="Test Radio 3">'.PHP_EOL;
		$this->assertEquals($radio3, $reflected->invokeArgs($form, array('radio', 'radbtn', 'Test Radio 3', true, array('class' => 'radbtn'))));
	}

	/**
	 * FormTest::test_checkbox()
	 *
	 * A test method for the checkbox creation method
	 *
	 * @see Laravel/Form::checkbox
	 * @depends test_checkable
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_checkbox() {
		$input1 = '<input id="" type="checkbox" name="" value="">'.PHP_EOL;
		$this->assertEquals($input1, Form::checkbox('', '', '', array()));

		$input2 = '<input type="checkbox">'.PHP_EOL;
		$this->assertEquals($input2, Form::checkbox(null, null, null, array()));

		$input3 = '<input checked="checked" type="checkbox" name="ckbox" value="Test Value">'.PHP_EOL;
		$this->assertEquals($input3, Form::checkbox('ckbox', 'Test Value', 1, array()));

		$input4 = '<input class="inp_check" checked="checked" type="checkbox" name="ckbox" value="Test Value 2">'.PHP_EOL;
		$this->assertEquals($input4, Form::checkbox('ckbox', 'Test Value 2', 1, array('class' => 'inp_check')));
	}

	/**
	 * FormTest::test_radio()
	 *
	 * A test method for the radio button creation method
	 *
	 * @see Laravel/Form::radio
	 * @depends test_checkable
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_radio() {
		$input1 = '<input id="" type="radio" name="" value="">'.PHP_EOL;
		$this->assertEquals($input1, Form::radio('', '', '', array()));

		$input2 = '<input type="radio">'.PHP_EOL;
		$this->assertEquals($input2, Form::radio(null, null, null, array()));

		$input3 = '<input checked="checked" type="radio" name="radbtn" value="Test Value">'.PHP_EOL;
		$this->assertEquals($input3, Form::radio('radbtn', 'Test Value', 1, array()));

		$input4 = '<input class="inp_radbtn" checked="checked" type="radio" name="radbtn" value="Test Value 2">'.PHP_EOL;
		$this->assertEquals($input4, Form::radio('radbtn', 'Test Value 2', 1, array('class' => 'inp_radbtn')));
	}

	/**
	 * FormTest::test_submit()
	 *
	 * A test method for the submit button convenience method
	 *
	 * @see Laravel/Form::submit
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_submit() {
		$submit1 = '<input type="submit">'.PHP_EOL;
		$this->assertEquals($submit1, Form::submit(null));

		$submit2 = '<input type="submit" value="">'.PHP_EOL;
		$this->assertEquals($submit2, Form::submit(''));

		$submit3 = '<input type="submit" value="test_submit">'.PHP_EOL;
		$this->assertEquals($submit3, Form::submit('test_submit'));

		$submit4 = '<input class="inp_submit" type="submit" value="test_submit">'.PHP_EOL;
		$this->assertEquals($submit4, Form::submit('test_submit', array('class' => 'inp_submit')));

		$submit5 = '<input name="submit_onething" type="submit" value="test_submit">'.PHP_EOL;
		$this->assertEquals($submit5, Form::submit('test_submit', array('name' => 'submit_onething')));
	}

	/**
	 * FormTest::test_reset()
	 *
	 * A test method for the reset button convenience method
	 *
	 * @see Laravel/Form::reset
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_reset() {
		$reset1 = '<input type="reset">'.PHP_EOL;
		$this->assertEquals($reset1, Form::reset(null));

		$reset2 = '<input type="reset" value="">'.PHP_EOL;
		$this->assertEquals($reset2, Form::reset(''));

		$reset3 = '<input type="reset" value="test_reset">'.PHP_EOL;
		$this->assertEquals($reset3, Form::reset('test_reset'));

		$reset4 = '<input class="inp_reset" type="reset" value="test_reset">'.PHP_EOL;
		$this->assertEquals($reset4, Form::reset('test_reset', array('class' => 'inp_reset')));

		$reset5 = '<input name="reset_onething" type="reset" value="test_reset">'.PHP_EOL;
		$this->assertEquals($reset5, Form::reset('test_reset', array('name' => 'reset_onething')));
	}

	/**
	 * FormTest::test_image()
	 *
	 * A test method for the image convenience method
	 *
	 * @see Laravel/Form::image
	 * @depends test_input
	 * @access public
	 * @return void
	 */
	public function test_image() {
		$image1 = '<input src="http://localhost/" type="image">'.PHP_EOL;
		$this->assertEquals($image1, Form::Image(''));

		$image2 = '<input src="http://localhost/" type="image">'.PHP_EOL;
		$this->assertEquals($image2, Form::Image(null));

		$image3 = '<input src="http://localhost/resources/img/test.png" type="image">'.PHP_EOL;
		$this->assertEquals($image3, Form::Image('/resources/img/test.png'));

		$image4 = '<input src="http://localhost/resources/img/test.png" type="image" name="test_img">'.PHP_EOL;
		$this->assertEquals($image4, Form::Image('/resources/img/test.png', 'test_img'));

		$image4 = '<input class="inp_img" src="http://localhost/resources/img/test.png" type="image" name="test_img">'.PHP_EOL;
		$this->assertEquals($image4, Form::Image('/resources/img/test.png', 'test_img', array('class' => 'inp_img')));

		$image5 = '<input src="http://localhost/captcha.php" type="image">'.PHP_EOL;
		$this->assertEquals($image5, Form::Image('captcha.php'));
	}

	/**
	 * FormTest::test_button()
	 *
	 * A test method for the button creation tag
	 *
	 * @see Laravel/Form::button
	 * @access public
	 * @return void
	 */
	public function test_button() {
		$button1 = '<button></button>'.PHP_EOL;
		$this->assertEquals($button1, Form::button(''));

		$button2 = '<button></button>'.PHP_EOL;
		$this->assertEquals($button2, Form::button(null, null));

		$button3 = '<button>Test Button Alpha</button>'.PHP_EOL;
		$this->assertEquals($button3, Form::button('Test Button Alpha'));

		$button4 = '<button class="btn">Test Button Alpha</button>'.PHP_EOL;
		$this->assertEquals($button4, Form::button('Test Button Alpha', array('class' => 'btn')));
	}

	/**
	 * FormTest::test_id()
	 *
	 * A test method for the id lookup
	 *
	 * @see Laravel/Form::button
	 * @depends test_label
	 * @access public
	 * @return void
	 */
	public function test_id() {
		$reflected = self::get_method('id');
		$form = new Form();

		$this->assertEquals('test_id_1', $reflected->invokeArgs($form, array('test', array('id' => 'test_id_1'))));

		$label1 = '<label for="test_name">Test Value</label>'.PHP_EOL;
		$this->assertEquals($label1, Form::label('test_name', 'Test Value', array()));
		$this->assertEquals('test_name', $reflected->invokeArgs($form, array('test_name', array('class' => 'test_class'))));
	}

	/**
	 * FormTest::test_uber_form_creation()
	 *
	 * A test method to build a simple login form at the end
	 *
	 * @depends test_open
	 * @depends test_text
	 * @depends test_password
	 * @depends test_submit
	 * @access public
	 * @return void
	 */
	public function test_uber_form_creation() {
		$form = Form::open('/user/login', 'post', array('id' => 'frm_login'));

		$form .= Form::label('username', 'Username / Email');
		$form .= Form::text('username', 'Test User', array('class' => 'inp_txt'));

		$form .= Form::label('password', 'Password');
		$form .= Form::password('password', array('class' => 'inp_txt'));

		$form .= Form::reset('Reset Form');
		$form .= Form::submit('Log in');

		$form .= Form::close();

		$expected = <<<EOF
<form id="frm_login" method="POST" action="http://localhost/index.php/user/login" accept-charset="UTF-8">
<label for="username">Username / Email</label>
<input class="inp_txt" type="text" name="username" value="Test User" id="username">
<label for="password">Password</label>
<input class="inp_txt" type="password" name="password" id="password">
<input type="reset" value="Reset Form">
<input type="submit" value="Log in">
</form>
EOF;
		$this->assertEquals($expected, $form);
	}

	/********************/
	/* SUPPORT FUNCTIOS */
	/********************/

	/**
	 * FormTest::get_method
	 *
	 * Given the name of a protected function this method will copy it and allow
	 * it to be tested within this test class
	 *
	 * @param string $name The name of the funciton to be tested
	 * @return object The method so it can be tested
	 */
	protected static function get_method($name) {
		$class = new ReflectionClass('Form');
		$method = $class->getMethod($name);
		$method->setAccessible(true);
		return $method;
	}
}
?>
