<?php

class HashTest extends PHPUnit_Framework_TestCase {

	public function testMakeMethodReturnsSixtyCharacterString()
	{
		$this->assertEquals(60, strlen(Hash::make('test')));
	}

	public function testCheckMethodReturnsTrueWhenPasswordsMatch()
	{
		$this->assertTrue(Hash::check('test', Hash::make('test')));
	}

	public function testCheckMethodReturnsFalseWhenPasswordsDontMatch()
	{
		$this->assertFalse(Hash::check('test', Hash::make('something')));
	}

}

