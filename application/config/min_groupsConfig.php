<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */
 
$root = "//";

return array(
	
	/*-----
			Css				
					-----*/
	
	'css' => array(
		$root. 'fonts/Puritan/stylesheet.css',
		$root. 'css/todo.css',
		$root. 'css/message.css',
		$root. 'js/CLEditor/jquery.cleditor.css',
		$root. 'css/jquery-ui.min.css'
	),
	
	/*-----
		  Javascript			
					-----*/
	'js' => array(
		$root. 'js/CLEditor/jquery.cleditor.js',
		$root. 'js/CLEditor/jquery.cleditor.xhtml.js',
		$root. 'js/todo.js',
	),
	
	'js_mobile' => array(
		$root. 'js/todo.js',
	),

);