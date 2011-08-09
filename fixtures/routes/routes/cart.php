<?php

return array(

	'GET /cart' => function()
	{
		//
	},

	'GET /cart/edit' => array('name' => 'edit_cart', function()
	{
		//
	}),

	'GET /cart/(:any)/(:num)' => array('name' => 'wildcard_cart', function($any, $num)
	{
		//
	}),

);