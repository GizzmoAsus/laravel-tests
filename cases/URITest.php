<?php use Laravel\URI;

class URITest {

	public function setUp()
	{
		$_SERVER = array();
		URI::$uri = null;
	}

	/**
	 * @dataProvider requestUriProvider
	 */
	public function test_correct_uri_is_returned_when_request_uri_is_used($uri, $expectation)
	{
		$_SERVER['REQUEST_URI'] = $uri;
		$this->assertEquals($expectation, Request::uri());
	}

	public function requestUriProvider()
	{
		return array(
			array('/index.php', '/'),
			array('/index.php/', '/'),
			array('http://localhost/user', 'user'),
			array('http://localhost/user/', 'user'),
			array('http://localhost/index.php', '/'),
			array('http://localhost/index.php/', '/'),
			array('http://localhost/index.php//', '/'),
			array('http://localhost/index.php/user', 'user'),
			array('http://localhost/index.php/user/', 'user'),
			array('http://localhost/index.php/user/profile', 'user/profile'),
		);
	}

}