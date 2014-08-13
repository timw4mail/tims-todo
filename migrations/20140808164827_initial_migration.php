<?php

use Phinx\Migration\AbstractMigration;

class InitialMigration extends AbstractMigration {

	/**
	 * Create basic database schema
	 */
	public function change()
	{
		// Session storage table
		if ( ! $this->hasTable('todo_ci_sessions'))
		{
			$this->table('todo_ci_sessions', [
				'id' => FALSE,
				'primary_key' => 'session_id'
			])->addColumn('session_id' , 'string', ['limit' => 40])
				->addColumn('ip_address', 'string', ['limit' => 40])
				->addColumn('user_agent', 'string', ['limit' => 255])
				->addColumn('last_activity', 'integer')
				->addColumn('user_data', 'text')
				->create();
		}

		// User table
		if ( ! $this->hasTable('todo_user'))
		{
			$this->table('todo_user')
				->addColumn('username', 'string', ['limit' => 255])
				->addColumn('password', 'string', ['limit' => 255])
				->addColumn('email', 'string', ['limit' => 128])
				->addColumn('enabled', 'integer', ['default' => 1])
				->addColumn('timezone', 'string', ['limit' => 32, 'default' => 'America/Detroit'])
				->addColumn('num_format', 'integer', ['default' => 0])
				->addColumn('reset_token', 'string', ['limit' => 128])
				->create();
		}

		// Group table
		if ( ! $this->hasTable('todo_group'))
		{
			$this->table('todo_group')
				->addColumn('name', 'string', ['limit' => 128])
				->create();

			// Seed data
			$this->execute("INSERT INTO todo_group VALUES (0, 'global');");
		}

		// Category table
		if ( ! $this->hasTable('todo_category'))
		{
			$this->table('todo_category')
				->addColumn('title', 'string', ['limit' => 128])
				->addColumn('description', 'text', ['null' => FALSE])
				->addColumn('group_id', 'integer', ['default' => 0])
				//->addForeignKey('group_id', 'todo_group', 'id')
				->create();

			// Seed the data
			$this->execute("
				INSERT INTO todo_category VALUES (1, 'Work', 'Tasks related to work', 0);
				INSERT INTO todo_category VALUES (7, 'Optional ', 'Tasks that are not necessary, but it would be nice to see them completed.', 0);
				INSERT INTO todo_category VALUES (10, 'School', 'School related tasks', 0);
				INSERT INTO todo_category VALUES (11, 'Other', 'Tasks that don''t fit in another category.', 0);
				INSERT INTO todo_category VALUES (13, 'Personal', 'Personal tasks to do', 0);
			");
		}

		// Priority list table
		if ( ! $this->hasTable('todo_priority'))
		{
			$this->table('todo_priority')
				->addColumn('value', 'string')
				->create();

			// Seed the data
			$this->execute("
				INSERT INTO todo_priority VALUES (1, 'Optional');
				INSERT INTO todo_priority VALUES (2, 'Lowest');
				INSERT INTO todo_priority VALUES (3, 'Lower');
				INSERT INTO todo_priority VALUES (4, 'Low');
				INSERT INTO todo_priority VALUES (5, 'Normal');
				INSERT INTO todo_priority VALUES (6, 'High');
				INSERT INTO todo_priority VALUES (7, 'Higher');
				INSERT INTO todo_priority VALUES (8, 'Highest');
				INSERT INTO todo_priority VALUES (9, 'Immediate');
			");
		}

		// Status list table
		if ( ! $this->hasTable('todo_status'))
		{
			$this->table('todo_status')
				->addColumn('value', 'string')
				->create();

			// Seed the data
			$this->execute("
				INSERT INTO todo_status VALUES (3, 'In Progress');
				INSERT INTO todo_status VALUES (4, 'On Hold');
				INSERT INTO todo_status VALUES (5, 'Canceled');
				INSERT INTO todo_status VALUES (2, 'Completed');
				INSERT INTO todo_status VALUES (1, 'Created');
			");
		}

		// Task table
		if ( ! $this->hasTable('todo_item'))
		{
			$this->table('todo_item')
				->addColumn('user_id', 'integer')
				->addColumn('category_id', 'integer')
				->addColumn('priority', 'integer')
				->addColumn('status', 'integer', ['default' => 0])
				->addColumn('title', 'string', ['limit' => 128])
				->addColumn('description', 'text', ['null' => FALSE])
				->addColumn('due', 'integer', ['default' => 0])
				->addColumn('modified', 'integer')
				->addColumn('created', 'integer')
				->addForeignKey('category_id', 'todo_category', 'id')
				->addForeignKey('priority', 'todo_priority', 'id')
				->addForeignKey('status', 'todo_status', 'id')
				->addForeignKey('user_id', 'todo_user', 'id')
				->create();
		}

		// Checklist table
		if ( ! $this->hasTable('todo_checklist'))
		{
			$this->table('todo_checklist')
				->addColumn('task_id', 'integer')
				->addColumn('desc', 'string', ['limit' => 128])
				->addColumn('is_checked', 'integer')
				->addForeignKey('task_id', 'todo_item', 'id')
				->create();
		}


		// Group task sharing table
		if ( ! $this->hasTable('todo_group_task_link'))
		{
			$this->table('todo_group_task_link', [
				'id' => FALSE,
				'primary_key' => ['group_id', 'task_id']
			])->addColumn('group_id', 'integer')
				->addColumn('task_id', 'integer')
				->addColumn('permissions', 'integer')
				->addForeignKey('group_id', 'todo_group', 'id')
				->addForeignKey('task_id', 'todo_item', 'id')
				->create();
		}

		// Group user sharing table
		if ( ! $this->hasTable('todo_group_users_link'))
		{
			$this->table('todo_group_users_link', [
				'id' => FALSE,
				'primary_key' => ['group_id', 'user_id']
			])->addColumn('group_id', 'integer')
				->addColumn('user_id', 'integer')
				->addColumn('is_admin', 'integer')
				->addForeignKey('group_id', 'todo_group', 'id')
				->addForeignKey('user_id', 'todo_user', 'id')
				->create();
		}

		// Task comments table
		if ( ! $this->hasTable('todo_item_comments'))
		{
			$this->table('todo_item_comments')
				->addColumn('user_id', 'integer')
				->addColumn('item_id', 'integer')
				->addColumn('comment', 'text')
				->addColumn('time_posted', 'integer')
				->addColumn('status', 'integer')
				->addForeignKey('item_id', 'todo_item', 'id')
				->addForeignKey('status', 'todo_status', 'id')
				->addForeignKey('user_id', 'todo_user', 'id')
				->create();
		}

		// Reminder table
		if ( ! $this->hasTable('todo_reminder'))
		{
			$this->table('todo_reminder')
				->addColumn('task_id', 'integer')
				->addColumn('reminder_time', 'integer')
				->addColumn('sent', 'integer', ['default' => 0])
				->addColumn('user_id', 'integer')
				->addForeignKey('task_id', 'todo_item', 'id')
				->addForeignKey('user_id', 'todo_user', 'id', [
					'update' => 'cascade',
					'delete' => 'cascade'
				])->create();
		}

		// Friend link table
		if ( ! $this->hasTable('todo_user_friend_link'))
		{
			$this->table('todo_user_friend_link', [
				'id' => FALSE,
				'primary_key' => ['user_id', 'user_friend_id']
			])->addColumn('user_id', 'integer')
				->addColumn('user_friend_id', 'integer')
				->addColumn('confirmed', 'integer', ['default' => -1])
				->addForeignKey('user_friend_id', 'todo_user', 'id')
				->addForeignKey('user_id', 'todo_user', 'id')
				->create();
		}

		// Task shared by user table
		if ( ! $this->hasTable('todo_user_task_link'))
		{
			$this->table('todo_user_task_link', [
				'id' => FALSE,
				'primary_key' => ['task_id', 'user_id']
			])->addColumn('user_id', 'integer')
				->addColumn('task_id', 'integer')
				->addColumn('permissions', 'integer')
				->addForeignKey('task_id', 'todo_item', 'id')
				->addForeignKey('user_id', 'todo_user', 'id')
				->create();
		}
	}
}