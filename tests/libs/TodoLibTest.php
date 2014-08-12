<?php

class TodoLibTest extends Todo_TestCase {

	protected $tables = [
		'todo_priority' => 'todo_priority',
		'todo_category' => 'todo_category',
		'todo_user' => 'todo_user',
		'todo_group' => 'todo_group',
		'todo_category' => 'todo_category',
		'todo_user_friend_link' => 'todo_user_friend_link',
	];

	public function setUp()
	{
		parent::setUp();
		$this->CI->load->library('todo');
		$this->dbfixt('todo_priority', 'todo_category');
	}

	public function testGetUserFromId()
	{
		$expected = 'timw4mail';
		$actual = $this->CI->todo->get_user_from_id(1);

		$this->assertEquals($expected, $actual);
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

	public function testGetFriendRequests()
	{
		$this->create_session();
		$this->CI->session->set_userdata([
			'username' => 'timw4mail',
			'uid' => 1
		]);

		$expected = 1;

		$actual = $this->CI->todo->get_friend_requests();

		$this->assertEquals($expected, $actual);
	}

	public function dataValidatePass()
	{
		return [
			'passwords do not match' => [
				'post' => [
					'pass' => 'foo',
					'pass1' => 'bar',
					'old_pass' => 'guest'
				],
				'expected' => [
					'Passwords do not match.',
				]
			],
			'wrong password' => [
				'post' => [
					'pass' => 'foobar',
					'pass1' => 'foobar',
					'old_pass' => 'bazisbest'
				],
				'expected' => [
					'Wrong password'
				]
			],
			'valid password change request' => [
				'post' => [
					'pass' => 'foobar',
					'pass1' => 'foobar',
					'old_pass' => 'guest'
				],
				'expected' => TRUE
			]
		];
	}

	/**
	 * @dataProvider dataValidatePass
	 */
	public function testValidatePass($post, $expected)
	{
		$this->create_session();
		$this->CI->session->set_userdata([
			'username' => 'guest',
			'uid' => 3
		]);

		$_POST = $post;

		$actual = $this->CI->todo->validate_pass();

		$this->assertEquals($expected, $actual);
	}

	public function testCategoryList()
	{
		$this->create_session();
		$this->CI->session->set_userdata([
			'username' => 'timw4mail',
			'uid' => 1
		]);

		$expected = [
			array (
				'id' => '9',
				'title' => 'Tim\'s Todo',
				'description' => 'Tasks having to do with this application',
				'group_id' => '11',
			),
			array (
				'id' => '7',
				'title' => 'Optional ',
				'description' => 'Tasks that are not necessary, but it would be nice to see them completed.',
				'group_id' => '0',
			),
			array (
				'id' => '11',
				'title' => 'Other',
				'description' => 'Tasks that don\'t fit in another category.',
				'group_id' => '0',
			),
			array (
				'id' => '13',
				'title' => 'Personal',
				'description' => 'Personal tasks to do',
				'group_id' => '0',
			),
			array (
				'id' => '10',
				'title' => 'School',
				'description' => 'School related tasks',
				'group_id' => '0',
			),
			array (
				'id' => '1',
				'title' => 'Work',
				'description' => 'Tasks related to work',
				'group_id' => '0',
			),
		];

		$actual = $this->CI->todo->get_category_list();

		$this->assertEquals($expected, $actual);
	}

	public function testPriorityList()
	{
		$expected = $this->todo_priority_fixt;
		$actual = $this->CI->todo->get_priorities();

		$this->assertEquals($expected, $actual);
	}

	public function dataGetFriendList()
	{
		return [
			[
				'session' => [
					'username' => 'timw4mail',
					'uid' => 1
				],
				'expected' => [[
					'user_friend_id' => '1',
    				'uid' => '7',
    				'username' => 'aviat4ion',
				]]
			],
			[
				'session' => [
					'username' => 'aviat4ion',
					'uid' => 7
				],
				'expected' => [[
					'user_friend_id' => '1',
    				'uid' => '7',
    				'username' => 'timw4mail',
				]]
			],
			[
				'session' => [
					'username' => 'guest',
					'uid' => 3
				],
				'expected' => []
			]
		];
	}

	/**
	 * @dataProvider dataGetFriendList
	 */
	public function testGetFriendList($session, $expected)
	{
		$this->create_session($session);
		$actual = $this->CI->todo->get_friend_list();
		$this->assertEquals($expected, $actual);
	}

	public function dataGetUserAccountById()
	{
		return [
			'timw4mail' => [
				'user_id' => 1,
				'expected' => [
					'timezone' => 'America/Detroit',
					'user' => 'timw4mail',
					'email' => 'tim@timshomepage.net',
					'num_format' => '1',
				]
			],
			'aviat4ion' => [
				'user_id' => 7,
				'expected' => [
					'timezone' => 'America/Detroit',
					'user' => 'aviat4ion',
					'email' => 'timw4mail@gmail.com',
					'num_format' => '0',
				]
			],
			'guest' => [
				'user_id' => 3,
				'expected' => [
					'timezone' => 'America/Detroit',
					'user' => 'guest',
					'email' => 'guest@timshomepage.net',
					'num_format' => '0',
				]
			]
		];
	}

	/**
	 * @dataProvider dataGetUserAccountById
	 */
	public function testGetUserAccountById($user_id, $expected)
	{
		$actual = $this->CI->todo->get_user_account_by_id($user_id);
		$this->assertEquals($expected, $actual);
	}

	public function dataGetGroups()
	{
		return [
			[
				'user_id' => 1,
				'expected' => []
			],/*[
				'user_id' => 3,
				'expected' => [
					array (
						'id' => '62',
						'name' => 'shared',
					),
				]
			]*/
		];
	}

	/**
	 * @dataProvider dataGetGroups
	 */
	public function testGetGroups($user_id, $expected)
	{
		$actual = $this->CI->todo->get_group_list($user_id);
		$this->assertEquals($expected, $actual);
	}

	public function dataVerifyUser()
	{
		return [
			'valid username and password' => [
				'post' => [
					'user' => 'guest',
					'pass' => 'guest'
				],
				'expected' => TRUE
			],
			'valid email and password' => [
				'post' => [
					'user' => 'guest@timshomepage.net',
					'pass' => 'guest'
				],
				'expected' => TRUE
			],
			'invalid login' => [
				'post' => [
					'user' => 'timw4mail',
					'pass' => 'foobarbaz'
				],
				'expected' => 'Invalid username or password'
			]
		];
	}

	/**
	 * @dataProvider dataVerifyUser
	 */
	public function testVerifyUser($post, $expected)
	{
		$_POST = $post;
		$actual = $this->CI->todo->verify_user();
		$this->assertEquals($expected, $actual);
	}

	public function testGetFriendsInGroup()
	{
$this->markTestSkipped();
		$expected = [
			array (
				'user_id' => '7',
			),
			array (
				'user_id' => '3',
			)
		];
		$actual = $this->CI->todo->get_friends_in_group(62);
		$this->assertEquals($expected, $actual);
	}
	
	public function dataGetGroupNameById()
	{
		return [
			[
				'group_id' => 11,
				'expected' => 'timw4mail'
			],
			[
				'group_id' => 0,
				'expected' => 'global'
			],
			[
				'group_id' => 62,
				'expected' => 'shared'
			]
		];
	}

	/**
	 * @dataProvider dataGetGroupNameById
	 */
	public function testGetGroupNameById($group_id, $expected)
	{
		$actual = $this->CI->todo->get_group_name_by_id($group_id);
		$this->assertEquals($expected, $actual);
	}

	public function dataGetCategory()
	{
		return [
			[
				'cat_id' => 1,
				'expected' => [
					'title' => 'Work',
					'description' => 'Tasks related to work',
				]
			],
			[
				'cat_id' => 5,
				'expected' => NULL
			],
			[
				'cat_id' => 9,
				'expected' => [
					'title' => 'Tim\'s Todo',
					'description' => 'Tasks having to do with this application'
				]
			]
		];
	}

	/**
	 * @dataProvider dataGetCategory
	 */
	public function testGetCategory($cat_id, $expected)
	{
		$actual = $this->CI->todo->get_category($cat_id);
		$this->assertEquals($expected, $actual);
	}

	public function dataGetCategorySelect()
	{
		return [
			[
				'session' => [
					'username' => 'timw4mail',
					'uid' => 1
				],
				'expected' => T4 . '<option value="9">Tim\'s Todo</option>' . NL .
					T4 . '<option value="7">Optional </option>' . NL .
					T4 . '<option value="11">Other</option>' . NL .
					T4 . '<option value="13">Personal</option>' . NL .
					T4 . '<option value="10">School</option>' . NL .
					T4 . '<option value="1">Work</option>' . NL
			],
			[
				'session' => [
					'username' => 'guest',
					'uid' => 3
				],
				'expected' => T4 . '<option value="7">Optional </option>' . NL .
					T4 . '<option value="11">Other</option>' . NL .
					T4 . '<option value="13">Personal</option>' . NL .
					T4 . '<option value="10">School</option>' . NL .
					T4 . '<option value="1">Work</option>' . NL
			],
		];
	}

	/**
	 * @dataProvider dataGetCategorySelect
	 */
	public function testGetCategorySelect($session, $expected)
	{
		$this->create_session($session);
		$actual = $this->CI->todo->get_category_select();
		$this->assertEquals($expected, $actual);
	}

	public function testGetPrioritySelect()
	{
		$expected = T4 . '<option value="1" >Optional</option>' . NL .
			T4 . '<option value="2" >Lowest</option>' . NL .
			T4 . '<option value="3" >Lower</option>' . NL .
			T4 . '<option value="4" >Low</option>' . NL .
			T4 . '<option value="5" selected="selected">Normal</option>' . NL .
			T4 . '<option value="6" >High</option>' . NL .
			T4 . '<option value="7" >Higher</option>' . NL .
			T4 . '<option value="8" >Highest</option>' . NL .
			T4 . '<option value="9" >Immediate</option>' . NL;
		$actual = $this->CI->todo->get_priority_select();
		$this->assertEquals($expected, $actual);
	}
	
	public function dataGetGroupSelect()
	{
		return [
			/*[
				'user_id' => 3,
				'expected' => T4 . '<option value="62">shared</option>' . NL
			],*/
			[
				'user_id' => 1,
				'expected' => ''
			]
		];
	}

	/**
	 * @dataProvider dataGetGroupSelect()
	 */
	public function testGetGroupSelect($user_id, $expected)
	{
		$actual = $this->CI->todo->get_group_select($user_id);
		$this->assertEquals($expected, $actual);
	}
}
// End of TodoLibTest.php