<?php

/**
 * Extension of Session Library to
 * allow preliminary invalidation, and json-encoded session data
 */
class MY_Session extends CI_Session {

	/**
	 * Check if the session is valid
	 *
	 * @return bool
	 */
	public function session_valid()
	{
		$ip_address = $_SERVER['REMOTE_ADDR'];
		$user_agent = $_SERVER['HTTP_USER_AGENT'];

		$ip_blacklist = [
			'0.0.0.0',
			'127.0.0.1'
		];

		$ua_blacklist = [
			'false',
			FALSE,
			'',
			'PHPUnit'
		];

		if (in_array($ip_address, $ip_blacklist) || in_array($user_agent, $ua_blacklist))
		{
			$this->sess_destroy();
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Fetch/validate the current session data
	 *
	 * @return bool
	 */
	public function sess_read()
	{
		return ($this->session_valid()) ? parent::sess_read() : FALSE;
	}

	/**
	 * Validate the session before creation
	 */
	public function sess_create()
	{
		return ($this->session_valid()) ? parent::sess_create() : FALSE;
	}

	/**
	 * Serialize the session data to JSON
	 *
	 * @param array $data
	 * @return string
	 */
	public function _serialize($data)
	{
		return json_encode($data);
	}

	/**
	 * Unserialize the session data
	 *
	 * @param string $data
	 * @return mixed
	 */
	public function _unserialize($data)
	{
		return json_decode($data, TRUE);
	}
}
/* End of file MY_Session.php */
/* Location: ./application/libraries/MY_Session.php */