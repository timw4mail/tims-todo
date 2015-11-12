<?php
/**
 * Autoloader for test suite
 */

spl_autoload_register(function($class) {

	$paths = [
		'application/controllers',
		'application/models',
		'application/libraries',
		'application/core',
		'system/core',
		'system/libraries'
	];

	foreach($paths as $path)
	{
		$path = __DIR__ . "/../../{$path}/";
		$exact_file = "{$path}{$class}.php";
		$lower_file = $path . mb_strtolower($class) . ".php";

		foreach([$lower_file, $exact_file] as $file)
		{
			if (file_exists($file))
			{
				require_once($file);
				return;
			}
		}
	}
});