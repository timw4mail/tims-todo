<?php

class FriendModelTest extends Todo_TestCase {

	protected $tables = array(
		'todo_status' => 'todo_status',
		'todo_priority' => 'todo_priority',
		'todo_user' => 'todo_user',
		'todo_group' => 'todo_group',
		'todo_category' => 'todo_category',
		'todo_item' => 'todo_item',
		'todo_user_friend_link' => 'todo_user_friend_link',
		'todo_group_users_link' => 'todo_group_users_link'
	);

	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model('friend_model');
		$this->create_session();
	}

	public function testGetFriends()
	{
		$this->CI->session->set_userdata([
			'uid' => 7,
			'username' => 'aviat4ion'
		]);

		$expected = [
			7 => array (
				'user_friend_id' => '1',
				'uid' => '7',
				'username' => 'timw4mail',
				'email' => 'tim@timshomepage.net',
				'groups' =>
					array (
						0 => 'aviat4ion',
						1 => 'shared'
					),
			),
		];
		$actual = $this->CI->friend_model->get_friends();
		$this->assertEquals($expected, $actual);

		// Now test a lack of friends
		$this->CI->session->set_userdata([
			'uid' => 4,
			'username' => 'qwerty'
		]);

		$this->assertFalse($this->CI->friend_model->get_friends());
	}

	// --------------------------------------------------------------------------

	public function dataSendRequest()
	{
		return [
			'basic friend request' => [
				'session' => [
					'uid' => 7,
					'username' => 'aviat4ion'
				],
				'friend_id' => 3,
				'expected' => 1,

			],
			'double blind friend request' => [
				'session' => [
					'uid' => 3,
					'username' => 'guest'
				],
				'friend_id' => 7,
				'expected' => 1
			],
			'already a friend - friend request' => [
				'session' => [
					'uid' => 1,
					'username' => 'timw4mail'
				],
				'friend_id' => 7,
				'expected' => 0
			]
		];
	}

	/**
	 * @dataProvider dataSendRequest
	 */
	public function testSendRequest($session, $friend_id, $expected)
	{
		$this->CI->session->set_userdata($session);
		$actual = $this->CI->friend_model->send_request($friend_id);

		$this->assertEquals($expected, $actual);
	}

	// --------------------------------------------------------------------------

	public function testAcceptRequest()
	{
		$this->CI->session->set_userdata([
			'username' => 'timw4mail',
			'uid' => 1
		]);

		// Attempt to accept the request
		$this->assertEquals(1, $this->CI->friend_model->accept_request(3));

		// Verify the request was accepted
		$row = $this->CI->db->from('todo_user_friend_link')
			->where('user_id', 3)
			->where('user_friend_id', 1)
			->where('confirmed', 1)
			->get()
			->row();

		$this->assertNotEmpty($row, "Friend confirmation was persisted");
	}

	// --------------------------------------------------------------------------

	public function testRejectRequest()
	{
		$this->CI->session->set_userdata([
			'username' => 'timw4mail',
			'uid' => 1
		]);

		$this->assertEquals(1, $this->CI->friend_model->reject_request(3));

		// Verify the request was accepted
		$row = $this->CI->db->from('todo_user_friend_link')
			->where('user_id', 3)
			->where('user_friend_id', 1)
			->where('confirmed', 0)
			->get()
			->row();

		$this->assertNotEmpty($row, "Friend rejection was persisted");
	}

	// --------------------------------------------------------------------------

	public function dataFindFriends()
	{
		return [
			'Find yourself' => [
				'session' => [
					'username' => 'timw4mail',
					'uid' => 1
				],
				'q' => 'tim',
				'expected' => []
			],
			'Find current friend' => [
				'session' => [
					'username' => 'timw4mail',
					'uid' => 1
				],
				'q' => 'avi',
				'expected' => []
			],
			'Find possible new friend' => [
				'session' => [
					'username' => 'timw4mail',
					'uid' => 1
				],
				'q' => 'gue',
				'expected' => [
					[
						'id' => '3',
						'username' => 'guest',
						'email' => 'guest@timshomepage.net',
					]
				]
			],
			'Empty friend search' => [
				'session' => [
					'username' => 'timw4mail',
					'uid' => 1
				],
				'q' => '',
				'expected' => []
			],
			'No result friend search' => [
				'session' => [
					'username' => 'timw4mail',
					'uid' => 1
				],
				'q' => 'qwer',
				'expected' => []
			],
		];
	}

	/**
	 * @dataProvider dataFindFriends
	 */
	public function testFindFriends($session, $q, $expected)
	{
		$this->CI->session->set_userdata($session);
		$_GET['q'] = $q;

		$actual = $this->CI->friend_model->find_friends();

		$this->assertEquals($expected, $actual);
	}

	// --------------------------------------------------------------------------

	public function dataGetRequests()
	{
		return [
			'has friend request' => [
				'session' => [
					'username' => 'timw4mail',
					'uid' => 1
				],
				'expected' => [
					[
						'user_id' => '3',
						'username' => 'guest',
						'email' => 'guest@timshomepage.net',
					],
				]
			],
			'has no friend request' => [
				'session' => [
					'username' => 'aviat4ion',
					'uid' => 3
				],
				'expected' => FALSE
			]
		];
	}

	/**
	 * @dataProvider dataGetRequests
	 */
	public function testGetRequests($session, $expected)
	{
		$this->CI->session->set_userdata($session);

		$actual = $this->CI->friend_model->get_requests();

		$this->assertEquals($expected, $actual);
	}
}