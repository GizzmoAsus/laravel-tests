<?php

class CryptTest extends PHPUnit_Framework_TestCase {

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
		$this->assertEquals(Crypter::make()->decrypt(Crypter::make()->encrypt('test')), 'test');
	}

	/**
	 * @expectedException Exception
	 */
	public function testExceptionIsThrownWhenTryingToCryptWithoutKey()
	{
		Config::set('application.key', '');
		Crypter::make()->encrypt('test');
	}

}