<?php

class MessagesTest extends PHPUnit_Framework_TestCase {

	/**
	 * The Messages instance.
	 *
	 * @var Messages
	 */
	public $messages;

	public function setUp()
	{
		$this->messages = new System\Messages;
	}

	public function testAddingMessagesDoesNotCreateDuplicateMessages()
	{
		$this->messages->add('email', 'test');
		$this->messages->add('email', 'test');
		$this->assertEquals(count($this->messages->messages), 1);
	}

	public function testAddMethodPutsMessageInMessagesArray()
	{
		$this->messages->add('email', 'test');
		$this->assertArrayHasKey('email', $this->messages->messages);
		$this->assertEquals('test', $this->messages->messages['email'][0]);
	}

	public function testFirstMethodReturnsSingleString()
	{
		$this->messages->add('email', 'test');
		$this->assertEquals($this->messages->first('email'), 'test');
		$this->assertEquals($this->messages->first('something'), '');
	}

	public function testGetMethodReturnsAllMessagesForAttribute()
	{
		$messages = array('email' => array('something', 'else'));
		$this->messages->messages = $messages;
		$this->assertEquals($this->messages->get('email'), array('something', 'else'));
	}

	public function testAllMethodReturnsAllErrorMessages()
	{
		$messages = array('email' => array('something', 'else'), 'name' => array('foo'));
		$this->messages->messages = $messages;
		$this->assertEquals($this->messages->all(), array('something', 'else', 'foo'));
	}

	public function testMessagesRespectFormat()
	{
		$this->messages->add('email', 'test');
		$this->assertEquals($this->messages->first('email', '<p>:message</p>'), '<p>test</p>');
		$this->assertEquals($this->messages->get('email', '<p>:message</p>'), array('<p>test</p>'));
		$this->assertEquals($this->messages->all('<p>:message</p>'), array('<p>test</p>'));
	}

}