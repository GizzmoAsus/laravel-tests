<?php

class Blog_Controller extends Controller {

	public function action_index()
	{
		return 'blog@index';
	}

	public function action_post($id)
	{
		return 'post|'.$id;
	}

}