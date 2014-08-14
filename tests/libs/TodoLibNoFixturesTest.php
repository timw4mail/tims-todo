<?php

/**
 * Split of some methods that don't require database from TodoLibTest
 * Not having to load fixtures should make these run a lot faster
 */
class TodoLibNoFixturesTest extends Todo_TestCase {

	public function setUp()
	{
		parent::setUp();
		$this->CI->load->library('todo');
	}

	public function testCryptPass()
	{
		$expected = '$2y$10$qW8HlbNDNEJx1GqmYW9APOYOqo5apV8stjNcV/xunsvnjTYJBTc0m';
		$actual = $this->CI->todo->crypt_pass('guest');

		$this->assertNotEquals($expected, $actual,
			"Password has should be different every time it is used because of Bcrypt salt");
	}

	public function dataKanjiNum()
	{
		return [
			'non-numeric' => [
				'input' => 'string',
				'expected' => '〇'
			],
			'zero' => [
				'input' => 0,
				'expected' => '〇'
			],
			'one' => [
				'input' => 1,
				'expected' => '一'
			],
			'tens' => [
				'input' => 34,
				'expected' => '三十四'
			],
			'hundreds' => [
				'input' => 968,
				'expected' => '九百六十八'
			],
			'thousands' => [
				'input' => 1024,
				'expected' => '千二十四'
			],
			'ten thousands' => [
				'input' => 11275,
				'expected' => '万千二百七十五'
			],
			'hundred thousands' => [
				'input' => 658753,
				'expected' => '六十五万八千七百五十三'
			],
			'millions' => [
				'input' => 9876543,
				'expected' => '九百八十七万六千五百四十三'
			],
			'ten_millions' => [
				'input' => 98765432,
				'expected' => '九千八百七十六万五千四百三十二'
			],
			'hundred_millions' => [
				'input' => 987654321,
				'expected' => '九億八千七百六十五万四千三百二十一'
			]
		];
	}

	/**
	 * @dataProvider dataKanjiNum
	 */
	public function testKanjiNum($input, $expected)
	{
		$actual = $this->CI->todo->kanji_num($input);
		$this->assertEquals($expected, $actual);
	}

	public function dataRedirect303()
	{
		return [
			'full url redirect' => [
				'url' => 'http://www.example.com',
				'headers' => [
					array (
						'HTTP/1.1 303 See Other',
						true,
					),
					array (
						'Location:http://www.example.com',
						true,
					)
				]
			],
			'route redirect' => [
				'url' => 'task/list',
				'headers' => [
					array (
						'HTTP/1.1 303 See Other',
						true,
					),
					array (
						'Location:https://todo.timshomepage.net/task/list',
						true,
					)
				]
			]
		];
	}

	/**
	 * @dataProvider dataRedirect303
	 */
	public function testRedirect303($url, $headers)
	{
		$this->CI->todo->redirect_303($url);
		$actual = $this->CI->output->get_headers();

		$this->assertEquals($headers, $actual);
	}
}
// End of TodoLibNoFixturesTest