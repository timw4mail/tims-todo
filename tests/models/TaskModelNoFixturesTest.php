<?php

class TaskModelNoFixturesTest extends Todo_TestCase {

	public function setUp()
	{
		parent::setUp();
		$this->CI->load->model('task_model');
		$this->CI->form_validation->reset();
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
					'The Title field is required.',
					'The Description field is required.',
					'The Category field is required.',
					'The Priority field is required.',
					//'You must enter a due date in YYYY-MM-DD format.',
					'You must set a due date in order to get a reminder.',
					'You must put numeric hours and minutes for a reminder time.'
				]
			],
			'Empty task with bad due date' => [
				'post' => [
					'due' => '165743248576543152',
				],
				'expected' => [
					'The Title field is required.',
					'The Description field is required.',
					'The Category field is required.',
					'The Priority field is required.',
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
				/*'form_vals' => [
					'title' => 'A Test Task',
					'description' => 'A test task to validate with',
					'category' => 7,
					'due' => 1425873600,
					'due_minute' => FALSE
				],*/
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
				/*'form_vals' => [
					'title' => 'A Test Task',
					'description' => 'A test task to validate with',
					'category' => 7,
					'due' => 1425873600,
					'due_minute' => FALSE,
					'reminder' => TRUE,
					'rem_hours' => 4,
					'rem_minutes' => 30
				],*/
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
				/*'form_vals' => [
					'title' => 'A Test Task',
					'description' => 'A test task to validate with',
					'category' => 7,
					'due' => 1425873600,
					'due_minute' => FALSE,
				],*/
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
				/*'form_vals' => [
					'title' => 'A Test Task',
					'description' => 'A test task to validate with',
					'category' => 7,
					'due' => 1425873600,
					'due_minute' => FALSE,
				],*/
			],
		];
	}

	/**
	 * @dataProvider dataValidateTask
	 */
	public function testValidateTask($post, $expected)
	{
		$_POST = [];
		$_POST = $post;

		$actual = $this->CI->task_model->validate_task();

		// Verify the form validation data
		//$this->assertEquals($form_vals, $this->CI->task_model->form_vals);


		// Verify the function data
		$this->assertEquals($expected, $actual);
	}
}
// End of TaskModelNoFixturesTest