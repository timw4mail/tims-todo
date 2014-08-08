<?php
/**
 * Autoloader for test suite
 */

spl_autoload_register(function($class) {

	$paths = [

		'../application/controllers/',
		'../application/models/',
		'../application/libraries/',
		'../application/core/',
		'../system/core/',
		'../system/libraries/'
	];

	foreach($paths as $path)
	{
		$exact_file = "{$path}{$class}.php";
		$lower_file = $path . mb_strtolower($class) . ".php";

		if (file_exists($exact_file))
		{
			require_once($exact_file);
			return;
		}
		elseif (file_exists($lower_file))
		{
			require_once($lower_file);
			return;
		}
	}

});