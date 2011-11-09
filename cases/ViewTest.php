<?php

class ViewTest extends PHPUnit_Framework_TestCase {

	public function test_can_load_basic_view()
	{
		$view = View::make('test.basic');

		$this->assertTrue($view instanceof Laravel\View);
	}

	public function test_data_can_be_bound_to_view()
	{
		$view = View::make('test.basic');

		$view->with('name', 'Taylor');
		$this->assertEquals($view->name, 'Taylor');

		$view->name = 'Otwell';
		$this->assertEquals($view->name, 'Otwell');
	}

	public function test_views_can_be_created_by_name()
	{
		$this->assertTrue(View::of_home() instanceof Laravel\View);
	}

	public function test_basic_view_can_be_rendered()
	{
		$this->assertEquals(View::make('test.basic')->render(), '<h1>Test Basic</h1>');
	}

	public function test_bound_data_is_given_to_view()
	{
		$this->assertEquals(View::make('test.bound')->with('name', 'Taylor')->render(), '<h1>Taylor</h1>');
	}

	public function test_compiled_view_can_be_rendered()
	{
		if (file_exists($path = STORAGE_PATH.'views/'.md5('test.compiled')))
		{
			@unlink($path);
		}

		$this->assertFalse(file_exists($path));
		$this->assertEquals(View::make('test.compiled')->render(), '<h1>Compiled</h1>');
		$this->assertTrue(file_exists($path));
		$this->assertEquals(file_get_contents($path), '<h1>Compiled</h1>');

		$modified = filemtime($path);
		@unlink(VIEW_PATH.'test/compiled.blade.php');
		file_put_contents(VIEW_PATH.'test/compiled.blade.php', '<h1>Compiled</h1>');
		clearstatcache();

		$this->assertEquals(View::make('test.compiled')->render(), '<h1>Compiled</h1>');
		$this->assertTrue(file_exists($path));
		$this->assertEquals(file_get_contents($path), '<h1>Compiled</h1>');
		//$this->assertTrue(filemtime($path) > $modified);
	}

}