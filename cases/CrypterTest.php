<?php

class CrypterTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		Config::set('application.key', 'test-key');
	}

	public function tearDown()
	{
		Config::set('application.key', '');
	}

	public function testEncryptedValueCanBeDecryptedToOriginalValue()
	{
		$this->assertEquals('test', Crypter::decrypt(Crypter::encrypt('test')));
	}

}

