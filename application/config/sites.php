<?php
include "config.php";
/*
|--------------------------------------------------------------------------
| Content Domain
|--------------------------------------------------------------------------
|
| This is the domain used for serving content, such as css, javascript.
|
*/
$config['content_domain'] = $config['base_url'];

/*
|--------------------------------------------------------------------------
| Content Domain
|--------------------------------------------------------------------------
|
| This is the domain/subdomain used for serving images.
|
*/
$config['image_domain'] = $config['content_domain'].'/images';

/*
|--------------------------------------------------------------------------
| Static Lib Path
|--------------------------------------------------------------------------
|
| This is the path where the 'libs' directory is on the static domain.
|
*/
$config['static_lib_path'] = $config['content_domain'];

/*
|--------------------------------------------------------------------------
| Group Style/Javascript Path
|--------------------------------------------------------------------------
|
| This is the path that is used to determine the relative path to the
| stylesheet minifier. This should not need to be changed.
|
*/
$config['group_style_path'] = $config['static_lib_path'] . 'min/index.php?g=';

/*
|--------------------------------------------------------------------------
| Default Style Path
|--------------------------------------------------------------------------
|
| This is the path that is used to determine the relative path to the
| stylesheet minifier. This should not need to be changed.
|
*/
$config['style_path'] = $config['static_lib_path'] . '/min/index.php?b=css&amp;f=';

/*
|--------------------------------------------------------------------------
| Default Javascript Path
|--------------------------------------------------------------------------
|
| This is the path that is used to determine the relative path to the
| stylesheet minifier. This should not need to be changed.
|
*/
$config['script_path'] = $config['static_lib_path'] . '/min/index.php?b=js&amp;f=';


/*
|--------------------------------------------------------------------------
| Default title
|--------------------------------------------------------------------------
|
| Default title for webpages
|
*/

$config['default_title'] = "Tim's Todo";

/*
|--------------------------------------------------------------------------
| Default css group
|--------------------------------------------------------------------------
|
| Default css group
|
*/
$config['default_js_group'] = "js";
$config['default_css_group'] = "css";

/*
|--------------------------------------------------------------------------
| Ignore IPs
|--------------------------------------------------------------------------
|
| IP address that are not counted in stats
|
*/

$config['ignore_ips'] = array('127.0.0.1');

/*
|--------------------------------------------------------------------------
| Ignore UserAgents
|--------------------------------------------------------------------------
|
| UserAgents that are not counted in stats
|
*/

$config['ignore_user_agents'] = array();

/*
|--------------------------------------------------------------------------
| Enable Reminders
|--------------------------------------------------------------------------
|
| Only enable if you can set a cron job or scheduled task for the reminder feature
|
*/
$config['enable_reminders'] = TRUE;
