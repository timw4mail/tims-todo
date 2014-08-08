<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
*/

$route = [
	'default_controller' => 'login',
	
	'logout' => 'login/logout',
	'login' => 'login/do_login',
	'register' => 'login/register',
	'task/list' => 'task/list_tasks',
	'friend/list' => 'friend/friend_list',
	'category/list' => 'category/category_list',
	'task/category/list' => 'category/category_list',
	'task/calendar' => 'calendar/index',
	'task/calendar/:any' => 'calendar/index',
	'task/archive/:num' => 'task/archive',
	
	'404_overide' => ''
];

/* End of file routes.php */
/* Location: ./system/application/config/routes.php */