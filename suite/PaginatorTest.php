<?php

class PaginatorTest extends PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		class_alias('System\\Paginator', 'Paginator');
	}

	public function setUp()
	{
		$_GET['page'] = 1;

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['PATH_INFO'] = '/user';
	}

	public function testMakeMethodCreatesPaginator()
	{
		$this->assertInstanceOf('System\\Paginator', Paginator::make(array(), 30, 2));
		$this->assertEquals(Paginator::make(array(), 30, 2)->last_page, ceil(30 / 2));
		$this->assertEquals(Paginator::make(array(), 30, 2)->page, Paginator::page(30, 2));
	}

	public function testPageMethodReturnsValidPageNumber()
	{
		$this->assertEquals(1, Paginator::page(30, 5));

		$_GET['page'] = 0;
		$this->assertEquals(1, Paginator::page(30, 5));

		$_GET['page'] = 1.2;
		$this->assertEquals(1, Paginator::page(30, 5));

		$_GET['page'] = -100;
		$this->assertEquals(1, Paginator::page(30, 5));

		$_GET['page'] = 'a';
		$this->assertEquals(1, Paginator::page(30, 5));

		$_GET['page'] = 1000;
		$this->assertEquals(6, Paginator::page(30, 5));
	}

	public function testLinksMethodReturnsStringWithPageDiv()
	{
		$this->assertTrue(strpos(Paginator::make(array(), 30, 2)->links(), '<div class="pagination">') !== false);
		$this->assertTrue(strpos(Paginator::make(array(), 30, 2)->links(), '</div>') !== false);
		$this->assertTrue(strpos(Paginator::make(array(), 30, 2)->links(), 'Previous') !== false);
		$this->assertTrue(strpos(Paginator::make(array(), 30, 2)->links(), 'Next') !== false);
	}

	public function testNextMethodReturnsNext()
	{
		$this->assertTrue(strpos(Paginator::make(array(), 30, 2)->next(), 'Next &raquo;') !== false);
	}

	public function testPreviousMethodReturnsPrevious()
	{
		$this->assertTrue(strpos(Paginator::make(array(), 30, 2)->previous(), '&laquo; Previous') !== false);
	}

	public function testLangMethodSetsPaginatorLanguage()
	{
		$this->assertEquals(Paginator::make(array(), 30, 2)->lang('en')->language, 'en');
	}

}