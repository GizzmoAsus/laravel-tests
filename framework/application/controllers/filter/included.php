<?php

class Filter_Included_Controller extends Controller {

	public function __construct()
	{
		$this->filter('after', 'controller_after_2')->only('active');
	}

	public function action_active()
	{
		return 'active';
	}

	public function action_passive()
	{
		return 'passive';
	}

}