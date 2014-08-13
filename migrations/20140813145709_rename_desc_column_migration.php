<?php

use Phinx\Migration\AbstractMigration;

class RenameDescColumnMigration extends AbstractMigration
{
	/**
	 * Rename 'desc' field to 'description'. Reserved words suck
	 */
	public function change()
	{
		$this->table('todo_checklist')
			->renameColumn('desc', 'description');
	}
}