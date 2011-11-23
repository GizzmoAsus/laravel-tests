<?php

use Laravel\Messages;

class MessagesTest extends PHPUnit_Framework_TestCase {

	/**
	 * The Messages instance.
	 *
	 * @var Messages
	 */
	public $messages;

	public function setUp()
	{
		$this->messages = new Messages;
	}

	public function testAddingMessagesDoesNotCreateDuplicateMessages()
	{
		$this->messages->add('email', 'test');
		$this->messages->add('email', 'test');
		$this->assertCount(1, $this->messages->messages);
	}

	public function testAddMethodPutsMessageInMessagesArray()
	{
		$this->messages->add('email', 'test');
		$this->assertArrayHasKey('email', $this->messages->messages);
		$this->assertEquals('test', $this->messages->messages['email'][0]);
	}

	public function testHasMethodReturnsTrue()
	{
		$this->messages->add('email', 'test');
		$this->assertTrue($this->messages->has('email'));
	}

	public function testHasMethodReturnsFalse()
	{
		$this->assertFalse($this->messages->has('something'));
	}

	public function testFirstMethodReturnsSingleString()
	{
		$this->messages->add('email', 'test');
		$this->assertEquals('test', $this->messages->first('email'));
		$this->assertEquals('', $this->messages->first('something'));
	}

	public function testGetMethodReturnsAllMessagesForAttribute()
	{
		$messages = array('email' => array('something', 'else'));
		$this->messages->messages = $messages;
		$this->assertEquals(array('something', 'else'), $this->messages->get('email'));
	}

	public function testAllMethodReturnsAllErrorMessages()
	{
		$messages = array('email' => array('something', 'else'), 'name' => array('foo'));
		$this->messages->messages = $messages;
		$this->assertEquals(array('something', 'else', 'foo'), $this->messages->all());
	}

	public function testMessagesRespectFormat()
	{
		$this->messages->add('email', 'test');
		$this->assertEquals('<p>test</p>', $this->messages->first('email', '<p>:message</p>'));
		$this->assertEquals(array('<p>test</p>'), $this->messages->get('email', '<p>:message</p>'));
		$this->assertEquals(array('<p>test</p>'), $this->messages->all('<p>:message</p>'));
	}

}

