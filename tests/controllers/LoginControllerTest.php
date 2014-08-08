<?php

class LoginControllerTest extends Todo_TestCase {

	protected $tables = [
		'todo_user' => 'todo_user',
	];

	public function setUp()
	{
		$this->CI = set_controller('Login');
		$this->dbfixt('todo_user');

		// Hack to fix problem with CodeIgniter in this specific context
		if ($this->CI->db->conn_id === FALSE) $this->CI->db->db_connect();

		// Clear post super global
		$_POST = [];

		// Clear error messages from form validation library
		$this->CI->form_validation->reset();
	}

	public function dataValidateReg()
	{
		return [
			'registration_mismatched_passwords' => [
				'post' => [
					'user' => 'sweety_belle',
					'pass' => 'apple_bloom',
					'pass1' => 'scootaloo',
					'email' => 'rain@bow.dash'
				],
				'expected' => [
					'The Password Confirmation field does not match the Password field.'
				]
			],
			'registration_invalid_email' => [
				'post' => [
					'user' => 'scootaloo',
					'pass' => 'foo',
					'pass1' => 'foo',
					'email' => 'rain@bow'
				],
				'expected' => [
					'You must enter a valid email address.'
				]
			],
			'registration_existing_user' => [
				'post' => [
					'user' => 'guest',
					'pass' => 'foo',
					'pass1' => 'foo',
					'email' => 'foo@bar.com'
				],
				'expected' => [
					'The Username field must contain a unique value.'
				]
			],
			'registration_existing_user_and_email' => [
				'post' => [
					'user' => 'guest',
					'pass' => 'foo',
					'pass1' => 'foo',
					'email' => 'guest@timshomepage.net'
				],
				'expected' => [
					'The Email Address field must contain a unique value.',
					'The Username field must contain a unique value.'
				]
			],
			'registration_valid' => [
				'post' => [
					'user' => 'applesauce',
					'pass' => 'foobar',
					'pass1' => 'foobar',
					'email' => 'foobar@baz.com'
				],
				'expected' => TRUE
			]
		];
	}

	/**
	 * @dataProvider dataValidateReg
	 */
	public function testValidateReg($post, $expected)
	{
		$_POST = $post;
		$res = $this->CI->form_validation->run('login/register');
		$actual = $this->CI->form_validation->get_error_array();
		$actual = ($res === TRUE) ? $res : $actual;

		$this->assertEquals($expected, $actual);
	}
}