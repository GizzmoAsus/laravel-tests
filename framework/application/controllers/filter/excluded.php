<?php

class Filter_Excluded_Controller extends Controller {

	public function __construct()
	{
		$this->filter('after', 'controller_after_3')->except(array('passive', 'passive_2'));
	}

	public function action_active()
	{
		return 'active';
	}

	public function action_passive()
	{
		return 'passive';
	}

	public function action_passive_2()
	{
		return 'passive_2';
	}

}