<?php

return array(

	'GET /cart' => function()
	{
		//
	},

	'GET /cart/edit' => array('name' => 'edit-cart', function()
	{
		//
	}),

	'GET /cart/(:any)/(:num)' => array('name' => 'wildcard-cart', function($any, $num)
	{
		//
	}),

);