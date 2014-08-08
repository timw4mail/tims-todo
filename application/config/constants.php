<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Kanji Constants
|--------------------------------------------------------------------------
|
| Constants defining equivalent kanji for arabic numerals
|
*/

define('ZERO', '〇');
define('ONE', '一');
define('TWO', '二');
define('THREE', '三');
define('FOUR', '四');
define('FIVE', '五');
define('SIX', '六');
define('SEVEN', '七');
define('EIGHT', '八');
define('NINE', '九');
define('TEN', '十');
define('HUNDRED', '百');
define('THOUSAND', '千');
define('TEN_THOUSAND', '万');
define('HUNDRED_MILLION', '億');


/*
|--------------------------------------------------------------------------
| TYPE Constants
|--------------------------------------------------------------------------
|
| Constants defining magic numbers
|
*/

// Status constants
define('STATUS_CREATED', 1);
define('STATUS_COMPLETED', 2);
define('STATUS_IN_PROGRESS', 3);
define('STATUS_ON_HOLD', 4);
define('STATUS_CANCELED', 5);

// Permission constants
define('PERM_NO_ACCESS', -1);
define('PERM_READ_ACCESS', 0);
define('PERM_COMMENT_ACCESS',1);
define('PERM_CHECKLIST_ACCESS', 2);
define('PERM_WRITE_ACCESS', 3);
define('PERM_ADMIN_ACCESS', 9);

// Friend constants
define('FRIEND_NOT_CONFIRMED', -1);
define('FRIEND_CONFIRMED', 1);
define('FRIEND_REJECTED', 0);

/*
|--------------------------------------------------------------------------
| Formatting Constants
|--------------------------------------------------------------------------
|
| Constants for the Page library
|
*/

//Define some constants for formatting
define('NL', "\n");
define('T1', "\t");
define('T2', T1.T1);
define('T3', T2.T1);
define('T4', T2.T2);
define('T5', T3.T2);
define('T6', T3.T3);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 					'ab');
define('FOPEN_READ_WRITE_CREATE', 				'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 			'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/* End of file constants.php */
/* Location: ./system/application/config/constants.php */