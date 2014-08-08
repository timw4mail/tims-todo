<?php

/**
 * Check Session
 *
 * Checks that the current user has a valid session, and if not, redirects
 * to the login page.
 * @return null
 */
function check_session()
{
	$CI =& get_instance();

	if($CI->session->userdata('uid') == FALSE)
	{
		$referer = $CI->uri->uri_string();

		$white_list = [
			'login',
			'login/register',
			'register',
			'reminder/check_reminder',
		];

		if ( ! in_array($referer, $white_list))
		{
			//Redirect to login
			$CI->session->set_userdata('login_referer', $referer);
			$CI->todo->redirect_303('login');
		}
	}
}