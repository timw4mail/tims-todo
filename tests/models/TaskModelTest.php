<?php

class TaskModelTest extends Todo_TestCase {

	/**
	 * Fixtures to load into the database
	 *
	 * @var array
	 */
	protected $tables = array(
		'todo_item' => 'todo_item',
		'todo_checklist' => 'todo_checklist',
		'todo_item_comments' => 'todo_item_comments',
		'todo_user' => 'todo_user',
		'todo_group' => 'todo_group',
		'todo_group_task_link' => 'todo_group_task_link',
		'todo_group_users_link' => 'todo_group_users_link',
		'todo_user_task_link' => 'todo_user_task_link',
		'todo_reminder' => 'todo_reminder'
	);

	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model('task_model');
	}
	
	public function dataValidateTask()
	{
		return [
			'Empty task with reminder validation' => [
				'post' => [
					'due' => 'April 27, 2014',
					'reminder' => 'rem_true'
				],
				'expected' => [
					'You must give the task a title',
					'The task must have a description',
					'Select a task category',
					'You must set a due date in order to get a reminder.',
					'You must put numeric hours and minutes for a reminder time.',
				]
			],
			'Empty task with bad due date' => [
				'post' => [
					'due' => '165743248576543152',
				],
				'expected' => [
					'You must give the task a title',
					'The task must have a description',
					'Select a task category',
					'You must enter a due date in YYYY-MM-DD format.'
				]
			],
			'Simple task validation' => [
				'post' => [
					'title' => 'A Test Task',
					'desc' => 'A test task to validate with',
					'category' => 7,
					'priority' => 5,
					'due' => '2015-03-09',
				],
				'expected' => TRUE,
				'form_vals' => [
					'title' => 'A Test Task',
					'description' => 'A test task to validate with',
					'category' => 7,
					'due' => 1425873600,
					'due_minute' => FALSE
				],
			],
			'task validation with reminder' => [
				'post' => [
					'title' => 'A Test Task',
					'desc' => 'A test task to validate with',
					'category' => 7,
					'priority' => 5,
					'due' => '2015-03-09',
					'reminder' => 'rem_true',
					'rem_minutes' => 30,
					'rem_hours' => 4
				],
				'expected' => TRUE,
				'form_vals' => [
					'title' => 'A Test Task',
					'description' => 'A test task to validate with',
					'category' => 7,
					'due' => 1425873600,
					'due_minute' => FALSE,
					'reminder' => TRUE,
					'rem_hours' => 4,
					'rem_minutes' => 30
				],
			],
			'task validation group shared task' => [
				'post' => [
					'title' => 'A Test Task',
					'desc' => 'A test task to validate with',
					'category' => 7,
					'priority' => 5,
					'due' => '2015-03-09',
					'share' => TRUE,
					'group' => [
						'62'
					],
					'group_perms' => 2,
					'friend_perms' => -1
				],
				'expected' => TRUE,
				'form_vals' => [
					'title' => 'A Test Task',
					'description' => 'A test task to validate with',
					'category' => 7,
					'due' => 1425873600,
					'due_minute' => FALSE,
				],
			],
			'task validation user shared task' => [
				'post' => [
					'title' => 'A Test Task',
					'desc' => 'A test task to validate with',
					'category' => 7,
					'priority' => 5,
					'due' => '2015-03-09',
					'share' => TRUE,
					'friend' => [3,7],
					'friend_perms' => 2,
					'group_perms' => -1
				],
				'expected' => TRUE,
				'form_vals' => [
					'title' => 'A Test Task',
					'description' => 'A test task to validate with',
					'category' => 7,
					'due' => 1425873600,
					'due_minute' => FALSE,
				],
			],
		];
	}

	/**
	 * @dataProvider dataValidateTask
	 */
	public function testValidateTask($post, $expected, $form_vals = NULL)
	{
		$_POST = $post;

		$actual = $this->CI->task_model->validate_task();

		// Verify the form validation data
		$this->assertEquals($form_vals, $this->CI->task_model->form_vals);

		// Verify the function data
		$this->assertEquals($expected, $actual);
	}

	public function testGetTaskList()
	{
		$this->create_session([
			'uid' => 1,
			'username' => 'timw4mail'
		]);

		$expected = [
			1 => array (
				'id' => '151',
				'user_id' => '1',
				'category_id' => '9',
				'title' => 'Todo Improvements',
				'due' => '0',
				'modified' => '1405454179',
				'created' => '1404231517',
				'category' => 'Tim\'s Todo',
				'priority' => 'Highest',
				'status' => 'In Progress',
				'status_id' => '3',
				'overdue' => false,
			),
			2 => array (
				'id' => '97',
				'user_id' => '1',
				'category_id' => '11',
				'title' => 'A task to share',
				'due' => '1404849665',
				'modified' => '1404849665',
				'created' => '1287096417',
				'category' => 'Other',
				'priority' => 'Normal',
				'status' => 'In Progress',
				'status_id' => '3',
				'overdue' => true,
			),
		];
		$actual = $this->CI->task_model->get_task_list();

		$this->assertEquals($expected, $actual);
	}

	public function testGetOverdueTaskList()
	{
		$this->create_session([
			'uid' => 1,
			'username' => 'timw4mail'
		]);

		$expected = [
			1 => array (
				'id' => '97',
				'user_id' => '1',
				'category_id' => '11',
				'title' => 'A task to share',
				'due' => '1404849665',
				'modified' => '1404849665',
				'created' => '1287096417',
				'category' => 'Other',
				'priority' => 'Normal',
				'overdue' => false, // Always false on overdue list, to cut down on visual noise
				'status' => 'In Progress'
			),
		];

		$actual = $this->CI->task_model->get_overdue_task_list();
		$this->assertEquals($expected, $actual);

	}

	public function testGetArchivedTaskList()
	{
		$this->create_session([
			'uid' => 1,
			'username' => 'timw4mail'
		]);

		$expected = [
			1 => array(
				'id' => '155',
				'user_id' => '1',
				'category_id' => '13',
				'title' => 'Fix NAS Web Interface',
				'due' => '1406347200',
				'modified' => '1405603389',
				'created' => '1405467330',
				'category' => 'Personal',
				'priority' => 'Highest',
				'status' => 'Canceled',
				'status_id' => '5',
				'overdue' => false,
			),
			'num_rows' => 1
		];

		$actual = $this->CI->task_model->get_archived_task_list();
		$this->assertEquals($expected, $actual);
	}

	public function dataGetSharedTaskList()
	{
		return [
			'Group shared task' => [
				'session' => [
					'username' => 'guest',
					'uid' => 3
				],
				'expected' => [
					1 => array (
						'id' => '97',
						'user_id' => '3',
						'category_id' => '11',
						'priority' => 'Normal',
						'status' => 'In Progress',
						'title' => 'A task to share',
						'due' => '1404849665',
						'modified' => '1404849665',
						'created' => '1287096417',
						'category' => 'Other',
						'group_perms' => '4',
						'overdue' => true,
					),
				]
			],
			'User shared task and group shared task' => [
				'session' => [
					'username' => 'aviat4ion',
					'uid' => 7
				],
				'expected' => [
					1 => array (
						'id' => '97',
						'user_id' => '7',
						'category_id' => '11',
						'priority' => 'Normal',
						'status' => 'In Progress',
						'title' => 'A task to share',
						'due' => '1404849665',
						'modified' => '1404849665',
						'created' => '1287096417',
						'category' => 'Other',
						'group_perms' => '4',
						'overdue' => TRUE,
					),
					2 => array (
						'id' => '151',
						'user_id' => '7',
						'category_id' => '9',
						'priority' => 'Highest',
						'status' => 'In Progress',
						'title' => 'Todo Improvements',
						'due' => '0',
						'modified' => '1405454179',
						'created' => '1404231517',
						'category' => 'Tim\'s Todo',
						'group_perms' => '9',
						'overdue' => FALSE,
					),
				]
			],
			'No shared task' => [
				'session' => [
					'username' => 'timw4mail',
					'uid' => 1
				],
				'expected' => []
			]
		];
	}

	/**
	 * @dataProvider dataGetSharedTaskList
	 */
	public function testGetSharedTaskList($session, $expected)
	{
		$this->create_session($session);

		$actual = $this->CI->task_model->get_shared_task_list();
		$this->assertEquals($expected, $actual);
	}

	public function dataGetTaskById()
	{
		return [
			'Has a reminder' => [
				'session' => [
					'username' => 'guest',
					'uid' => 3
				],
				'task_id' => 97,
				'expected' => [
					'id' => '97',
					'user_id' => '1',
					'priority' => 'Normal',
					'title' => 'A task to share',
					'due' => '1404849665',
					'modified' => '1404849665',
					'created' => '1287096417',
					'description' => 'This is a test of shared tasks. Feel free to comment.<br />',
					'username' => 'timw4mail',
					'current_status' => 'In Progress',
					'cat_name' => 'Other',
					'friend_perms' => -1,
					'group_perms' => '4',
					'user_perms' => '4',
					'selected_groups' =>
						array (
							0 => '62',
						),
					'selected_friends' => false,
					'reminder' => true,
					'rem_hours' => 0,
					'rem_minutes' => -10127,
				]
			],
			'No reminder' => [
				'session' => [
					'username' => 'timw4mail',
					'uid' => 1
				],
				'task_id' => 151,
				'expected' => [
					'id' => '151',
					'user_id' => '1',
					'priority' => 'Highest',
					'title' => 'Todo Improvements',
					'due' => '0',
					'modified' => '1405454179',
					'created' => '1404231517',
					'description' => 'Things to clean up, improve, fix or add to the todo app to make it better.<br />',
					'username' => 'timw4mail',
					'current_status' => 'In Progress',
					'cat_name' => 'Tim\'s Todo',
					'friend_perms' => -1,
					'group_perms' => -1,
					'user_perms' => 9,
					'selected_groups' => false,
					'selected_friends' =>
						array (
							0 => '7',
						),
					'reminder' => false,
					'rem_hours' => 0,
					'rem_minutes' => 30,
				]
			]
		];
	}

	/**
	 * @dataProvider dataGetTaskById
	 */
	public function testGetTaskById($session, $task_id, $expected)
	{
		$this->create_session($session);

		$actual = $this->CI->task_model->get_task_by_id($task_id);
		$this->assertEquals($expected, $actual);
	}


	public function testGetTaskComments()
	{
		$expected = [
			array (
				'id' => '22',
				'user_id' => '3',
				'item_id' => '97',
				'comment' => 'This is a test comment',
				'time_posted' => '1405457296',
				'email' => 'guest@timshomepage.net',
				'status' => 'In Progress',
			),
			array (
				'id' => '25',
				'user_id' => '1',
				'item_id' => '97',
				'comment' => 'This is another test comment',
				'time_posted' => '1405457296',
				'email' => 'tim@timshomepage.net',
				'status' => 'In Progress',
			),
		];
		$actual = $this->CI->task_model->get_task_comments(97);

		$this->assertEquals($expected, $actual);
	}

	public function testGetChecklist()
	{
		$expected = [
			array (
				'id' => '18',
				'task_id' => '97',
				'desc' => 'Share this task',
				'is_checked' => '1',
			),
			array (
				'id' => '136',
				'task_id' => '97',
				'desc' => 'Allow un-sharing',
				'is_checked' => '1',
			),
		];
		$actual = $this->CI->task_model->get_checklist(97);
		$this->assertEquals($expected, $actual);
	}

	public function testGetDayTaskList()
	{
		$this->create_session([
			'username' => 'timw4mail',
			'uid' => 1
		]);

		$start = 1404187200; // July 1, 2014
		$end =  1406865599; // July 31, 2014

		// One result, since there are two tasks with due dates,
		// and one is canceled, and will not be listed
		$expected = [
			8 => '<li><a href="https://todo.timshomepage.net/task/view/97">A task to share</a><br /> due 04:01 PM</li>'
		];

		$actual = $this->CI->task_model->get_day_task_list($start, $end, 31);
		$this->assertEquals($expected, $actual);
	}

	public function dataGetCurrentStatusId()
	{
		return [
			[
				'task_id' => 97,
				'status_id' => 3
			],
			[
				'task_id' => 155,
				'status_id' => 5
			]
		];
	}

	/**
	 * @dataProvider dataGetCurrentStatusId
	 */
	public function testGetCurrentStatusId($task_id, $status_id)
	{
		$this->assertEquals($status_id, $this->CI->task_model->get_current_status_id($task_id));
	}

	public function dataGetStatusSelect()
	{
		return [
			'Don\'t pass status id' => [
				'task_id' => 97,
				'status_id' => NULL,
				'expected' => T5 . '<option value="3" selected="selected">In Progress</option>'.  NL .
					T5 . '<option value="4">On Hold</option>' .  NL .
					T5 . '<option value="5">Canceled</option>' .  NL .
					T5 . '<option value="2">Completed</option>' .  NL .
					T5 . '<option value="1">Created</option>' .  NL
			],
			'Pass status id' => [
				'task_id' => 155,
				'status_id' => 5,
				'expected' => T5. '<option value="3">In Progress</option>'.  NL .
					T5 . '<option value="4">On Hold</option>' .  NL .
					T5 . '<option value="5" selected="selected">Canceled</option>' .  NL .
					T5 . '<option value="2">Completed</option>' .  NL .
					T5 . '<option value="1">Created</option>' .  NL
			]
		];
	}

	/**
	 * @dataProvider dataGetStatusSelect
	 */
	public function testGetStatusSelect($task_id, $status_id, $expected)
	{
		$actual = $this->CI->task_model->get_status_select($task_id, $status_id);
		$this->assertEquals($expected, $actual);
	}

	public function dataGetPrioritySelect()
	{
		return [
			[
				'task_id' => 97,
				'expected' => T5 . '<option value="1">Optional</option>' . NL .
					T5 . '<option value="2">Lowest</option>' . NL .
					T5 . '<option value="3">Lower</option>' . NL .
					T5 . '<option value="4">Low</option>' . NL .
					T5 . '<option value="5" selected="selected">Normal</option>' . NL .
					T5 . '<option value="6">High</option>' . NL .
					T5 . '<option value="7">Higher</option>' . NL .
					T5 . '<option value="8">Highest</option>'. NL .
					T5 . '<option value="9">Immediate</option>' . NL
			],
			[
				'task_id' => 151,
				'expected' => T5 . '<option value="1">Optional</option>' . NL .
					T5 . '<option value="2">Lowest</option>' . NL .
					T5 . '<option value="3">Lower</option>' . NL .
					T5 . '<option value="4">Low</option>' . NL .
					T5 . '<option value="5">Normal</option>' . NL .
					T5 . '<option value="6">High</option>' . NL .
					T5 . '<option value="7">Higher</option>' . NL .
					T5 . '<option value="8" selected="selected">Highest</option>'. NL .
					T5 . '<option value="9">Immediate</option>' . NL
			]
		];
	}

	/**
	 * @dataProvider dataGetPrioritySelect
	 */
	public function testGetPrioritySelect($task_id, $expected)
	{
		$actual = $this->CI->task_model->get_priority_select($task_id);
		$this->assertEquals($expected, $actual);
	}

	public function dataGetCategorySelect()
	{
		return [
			'Has custom categories' => [
				'session' => [
					'username' => 'timw4mail',
					'uid' => 1
				],
				'task_id' => 97,
				'expected' => T5 . '<option value="7">Optional </option>' . NL .
					T5 . '<option value="11" selected="selected">Other</option>' . NL .
					T5 . '<option value="13">Personal</option>' . NL .
					T5 . '<option value="10">School</option>' . NL .
					T5 . '<option value="9">Tim\'s Todo</option>' . NL .
					T5 . '<option value="1">Work</option>' . NL
			],
			'No custom categories' => [
				'session' => [
					'username' => 'guest',
					'uid' => 3
				],
				'task_id' => 97,
				'expected' => T5 . '<option value="7">Optional </option>' . NL .
					T5 . '<option value="11" selected="selected">Other</option>' . NL .
					T5 . '<option value="13">Personal</option>' . NL .
					T5 . '<option value="10">School</option>' . NL .
					T5 . '<option value="1">Work</option>' . NL
			]
		];
	}

	/**
	 * @dataProvider dataGetCategorySelect
	 */
	public function testGetCategorySelect($session, $task_id, $expected)
	{
		$this->create_session($session);
		$actual = $this->CI->task_model->get_category_select($task_id);
		$this->assertEquals($expected, $actual);
	}
}