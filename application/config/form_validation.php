<?php
$config = [
	'task' => [
		[
			'field' => 'title',
			'label' => 'Title',
			'rules' => 'required|min_length[1]|max_length[255]|xss_clean'
		],
		[
			'field' => 'desc',
			'label' => 'Description',
			'rules' => 'required|min_length[1]|xss_clean'
		],
		[
			'field' => 'category',
			'label' => 'Category',
			'rules' => 'required|is_natural_no_zero'
		],
		[
			'field' => 'priority',
			'label' => 'Priority',
			'rules' => 'required|is_natural'
		],
		[
			'field' => 'due_hour',
			'label' => 'Due Hour',
			'rules' => 'less_than[24]|is_natural'
		],
		[
			'field' => 'due_minute',
			'label' => 'Due Minute',
			'rules' => 'less_than[61]|is_natural'
		],
		[
			'field' => 'due',
			'label' => 'Due Date',
			'rules' => 'callback_validate[due_date]'
		],
		[
			'field' => 'reminder',
			'label' => 'Reminder',
			'rules' => ''
		]
	],
	'login/register' => [
		[
			'field' => 'email',
			'label' => 'Email Address',
			'rules' => 'required|callback_validate[valid_email]|is_unique[user.email]'
		],
		[
			'field' => 'user',
			'label' => 'Username',
			'rules' => 'required|is_unique[user.username]'
		],
		[
			'field' => 'pass',
			'label' => 'Password',
			'rules' => 'required',
		],
		[
			'field' => 'pass1',
			'label' => 'Password Confirmation',
			'rules' => 'required|matches[pass]',
		]
	]
];