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
		define('MCRYPT_RAND', 'foo');
		$this->assertEquals(Crypt::decrypt(Crypt::encrypt('test')), 'test');
	}

	/**
	 * @expectedException Exception
	 */
	public function testExceptionIsThrownWhenTryingToCryptWithoutKey()
	{
		Config::set('application.key', '');
		Crypt::encrypt('test');
	}

}