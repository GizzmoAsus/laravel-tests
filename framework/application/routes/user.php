<?php

return array(

	'GET /user' => function()
	{
		return 'GET /user';
	},

	'GET /user/login' => array('name' => 'login', function()
	{
		return 'GET /user/login';
	}),

	'GET /user/profile/(:any)' => function($name)
	{
		return $name;
	},

	'GET /user/id/(:num)' => function($id)
	{
		return $id;
	},

	'GET /user/name/(:any)/(:num)' => function($name, $id)
	{
		return $name.'|'.$id;	
	},

	'GET /user/year/(:num?)' => function($year = 2011)
	{
		return $year;
	},

);