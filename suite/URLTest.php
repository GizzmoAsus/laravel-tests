<?php

class URLTest extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		Config::set('application.url', 'http://localhost');
		Config::set('application.index', 'index.php');
	}

	public function testToMethodProducesProperlyFormattedURL()
	{
		$this->assertEquals(URL::to('something'), 'http://localhost/index.php/something');
		$this->assertEquals(URL::to('something/'), 'http://localhost/index.php/something');
		$this->assertEquals(URL::to('something//'), 'http://localhost/index.php/something');
		$this->assertEquals(URL::to('/'), 'http://localhost/index.php/');
		$this->assertEquals(URL::to(''), 'http://localhost/index.php/');
	}

	public function testToMethodReturnsProperlyFormattedURLWhenNoIndexIsSet()
	{
		Config::set('application.index', '');

		$this->assertEquals(URL::to('something'), 'http://localhost/something');
		$this->assertEquals(URL::to('something/'), 'http://localhost/something');
		$this->assertEquals(URL::to('something//'), 'http://localhost/something');
		$this->assertEquals(URL::to('/'), 'http://localhost/');
		$this->assertEquals(URL::to('//'), 'http://localhost/');
		$this->assertEquals(URL::to(''), 'http://localhost/');		
	}

	public function testToMethodLeavesOutIndexForAssets()
	{
		$this->assertEquals(URL::to('something', false, true), 'http://localhost/something');
		$this->assertEquals(URL::to('something/', false, true), 'http://localhost/something');
		$this->assertEquals(URL::to('something//', false, true), 'http://localhost/something');
		$this->assertEquals(URL::to('/', false, true), 'http://localhost/');
		$this->assertEquals(URL::to('', false, true), 'http://localhost/');

		$this->assertEquals(URL::to_asset('something'), 'http://localhost/something');
		$this->assertEquals(URL::to_asset('something/'), 'http://localhost/something');
		$this->assertEquals(URL::to_asset('/'), 'http://localhost/');
		$this->assertEquals(URL::to_asset(''), 'http://localhost/');
	}

	public function testToMethodCanMakeSecureURLs()
	{
		$this->assertEquals(URL::to('something', true), 'https://localhost/index.php/something');
		$this->assertEquals(URL::to('something/', true), 'https://localhost/index.php/something');
		$this->assertEquals(URL::to('something//', true), 'https://localhost/index.php/something');
		$this->assertEquals(URL::to('/', true), 'https://localhost/index.php/');
		$this->assertEquals(URL::to('', true), 'https://localhost/index.php/');

		$this->assertEquals(URL::to_secure(''), 'https://localhost/index.php/');
		$this->assertEquals(URL::to_secure('something'), 'https://localhost/index.php/something');
	}

	public function testToRouteMethodCanCreateURLToRoute()
	{
		$this->assertEquals(URL::to_route('edit-cart'), 'http://localhost/index.php/cart/edit');
		$this->assertEquals(URL::to_route('wildcard-cart', array('test', 'foo'), true), 'https://localhost/index.php/cart/test/foo');
		$this->assertEquals(URL::to_secure_route('wildcard-cart', array('test', 'foo')), 'https://localhost/index.php/cart/test/foo');
	}

	public function testSlugMethodCreatesURLSlug()
	{
		$this->assertEquals(URL::slug('This is my article!!!'), 'this-is-my-article');
		$this->assertEquals(URL::slug('This is my article <> wow'), 'this-is-my-article-wow');
		$this->assertEquals(URL::slug('This is my article <> wow\'s'), 'this-is-my-article-wows');
		$this->assertEquals(URL::slug('Deuxième article'), 'deuxieme-article');
	}

}