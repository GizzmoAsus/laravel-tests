<?php

class ModuleLoaderTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		System\Loader::$modules = array('auth');
	}

	public function tearDown()
	{
		System\Loader::$modules = array();
	}

	public function testModelsAreLoadedCorrectly()
	{
		$model = new Auth\Model;
		$this->assertEquals($model->test(), 'test');
	}

	public function testLibrariesAreLoadedCorrectly()
	{
		$library = new Auth\Library;
		$this->assertEquals($library->test(), 'test');
	}

}