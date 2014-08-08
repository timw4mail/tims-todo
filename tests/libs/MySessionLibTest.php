<?php

class MySessionLibTest extends Todo_TestCase {

	public function setUp()
	{
		parent::setUp();
	}

	public function dataValidSession()
	{
		return [
			'bad_ip' => [
				'server' => [
					'REMOTE_ADDR' => '0.0.0.0',
					'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:30.0) Gecko/20100101 Firefox/30.0'
				],
				'expected' => FALSE
			],
			'bad_ua' => [
				'server' => [
					'REMOTE_ADDR' => '8.8.8.8',
					'HTTP_USER_AGENT' => FALSE
				],
				'expected' => FALSE
			],
			'good_session' => [
				'server' => [
					'REMOTE_ADDR' => '8.8.8.8',
					'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:30.0) Gecko/20100101 Firefox/30.0'
				],
				'expected' => TRUE
			]
		];
	}

	/**
	 * @dataProvider dataValidSession
	 * @param $server
	 * @param $expected
	 */
	public function testValidSession($server, $expected)
	{
		foreach($server as $key => $val)
		{
			$_SERVER[$key] = $val;
		}

		$actual = $this->CI->session->session_valid();

		$this->assertEquals($expected, $actual);
	}

	public function testSerialize()
	{
		$this->assertEquals('{}', $this->CI->session->_serialize(new stdClass));
		$this->assertEquals([], $this->CI->session->_unserialize('{}'));
	}
} 