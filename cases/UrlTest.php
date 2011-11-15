<?php use Laravel\URL, Laravel\Config;

class UrlTest extends PHPUnit_Framework_TestCase {

	public function test_simple_url()
	{
		$this->assertEquals('http://localhost/index.php/', URL::to(''));
		$this->assertEquals('http://localhost/index.php/something', URL::to('something'));
	}

	public function test_simple_url_without_index()
	{
		Config::set('application.index', '');

		$this->assertEquals('http://localhost/', Url::to(''));
		$this->assertEquals('http://localhost/something', Url::to('something'));

		Config::set('application.index', 'index.php');
	}

	public function test_asset_url()
	{
		$this->assertEquals('http://localhost/img/test.jpg', URL::to_asset('img/test.jpg'));

		Config::set('application.index', '');

		$this->assertEquals('http://localhost/img/test.jpg', URL::to_asset('img/test.jpg'));

		Config::set('application.index', 'index.php');
	}

	public function test_secure_url()
	{
		$this->assertEquals('https://localhost/index.php/something', URL::to_secure('something'));

		Config::set('application.ssl', false);

		$this->assertEquals('http://localhost/index.php/something', URL::to_secure('something'));

		Config::set('application.ssl', true);
	}

	public function test_slug()
	{
		$this->assertEquals('my-favorite-blog', URL::slug('My favorite blog!!'));
		$this->assertEquals('my_favorite_blog', URL::slug('My favorite blog!!', '_'));
	}

}
