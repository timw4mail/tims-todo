<?php

use Phinx\Migration\AbstractMigration;

class ViewCreationMigration extends AbstractMigration
{
	/**
	 * Change Method.
	 *
	 * More information on this method is available here:
	 * http://docs.phinx.org/en/latest/migrations.html#the-change-method
	 *
	 * Uncomment this method if you would like to use it.
	 *
	public function change()
	{
	}
	*/
	
	/**
	 * Migrate Up.
	 */
	public function up()
	{
		if ( ! $this->hasTable('todo_task_view'))
		{
			$this->execute("CREATE VIEW todo_task_view AS
				SELECT todo_item.id, todo_item.user_id, todo_item.category_id, todo_item.title, todo_item.due, todo_item.modified, todo_item.created, todo_category.title AS category, todo_priority.value AS priority, todo_status.value AS status, todo_status.id AS status_id FROM (((todo_item LEFT JOIN todo_category ON ((todo_category.id = todo_item.category_id))) LEFT JOIN todo_priority ON ((todo_priority.id = todo_item.priority))) LEFT JOIN todo_status ON ((todo_status.id = todo_item.status))) ORDER BY todo_item.due, todo_item.priority DESC, todo_item.created;
				");
		}
	}

	/**
	 * Migrate Down.
	 */
	public function down()
	{
		$this->execute('DROP VIEW todo_task_view');
	}
}