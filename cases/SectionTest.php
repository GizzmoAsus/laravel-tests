<?php

class SectionTest extends PHPUnit_Framework_TestCase {

	public function test_section_start_with_string_adds_section()
	{
		Section::start('title', 'My Title');
		$this->assertEquals('My Title', Section::$sections['title']);
	}

	public function test_section_start_opens_buffer()
	{
		$level = ob_get_level();
		Section::start('level');
		$this->assertEquals($level+1, ob_get_level());

		Section::stop();
	}

	public function test_section_buffer_adds_section()
	{
		Section::start('body'); // Pushes '' onto the section
		echo 'This is my body';
		Section::stop(); // Appends PHP_EOL.$content

		$this->assertEquals(PHP_EOL.'This is my body', Section::$sections['body']);
	}

	public function test_section_inject_appends_content()
	{
		Section::inject('name', 'Phill');
		Section::inject('name', 'Sparks');

		$this->assertEquals('Phill'.PHP_EOL.'Sparks', Section::$sections['name']);
	}

	public function test_section_yield_returns_content()
	{
		Section::inject('yield', '123');
		$this->assertEquals(Section::$sections['yield'], Section::yield('yield'));
	}

	public function test_section_yield_of_unknown_key_returns_empty()
	{
		$this->assertEquals('', Section::yield('unknown'));
	}

}
