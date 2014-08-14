<?php

// Autoloader for password compat
require_once(__DIR__ . '/../vendor/autoload.php');

// Require base bootstrap file
require_once(__DIR__ . '/../application/third_party/CIUnit/bootstrap_phpunit.php');

// Require autoloader
require_once(__DIR__ . '/env/autoloader.php');

/**
 * Noop Controller
 */
class Welcome extends CIU_Controller {}


/**
 * Base TestSuite
 *
 * @method bool assertEquals(mixed $expected, mixed $actual)
 * @method bool assertNotEquals(mixed $expected, mixed $actual)
 */
class Todo_TestCase extends CIUnit_TestCase {

	/**
	 * Setup for each test method
	 */
	public function setUp()
	{
		$this->CI = set_controller('welcome');
		parent::setUp();
	}

	/**
	 * Populates a session with mock data
	 *
	 * @param array $more_data
	 */
	public function create_session($more_data = [])
	{
		$data = [
			'ip_address' => '8.8.8.8',
			'user_agent' => 'PHPUnit',
			'last_activity' => '1234567890',
			'session_id' => 'jh38uckkjhcijedk'
		];
		
		$data = array_merge($data, $more_data);

		$this->CI->session->set_userdata($data);
	}
}