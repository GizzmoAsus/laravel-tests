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

}