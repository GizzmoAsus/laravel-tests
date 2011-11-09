<?php

class Filter_Basic_Controller extends Controller {

	public function __construct()
	{
		$this->filter('before', 'controller_before_1');
		$this->filter('after', 'controller_after_1');
	}

	public function action_before()
	{
		return 'action_before';
	}

	public function action_after()
	{
		return 'action_after';
	}

}