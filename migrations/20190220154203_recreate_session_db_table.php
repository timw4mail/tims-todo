<?php

use Phinx\Migration\AbstractMigration;

class RecreateSessionDbTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     */
    public function change()
    {
	    // Session storage table
		if ($this->hasTable('todo_ci_sessions'))
		{
			$this->table('todo_ci_sessions')->drop();

			$this->table('todo_ci_sessions', [
				'id' => FALSE,
				'primary_key' => 'id'
			])->addColumn('id' , 'string', ['limit' => 128, 'null' => FALSE])
				->addColumn('ip_address', 'string', ['limit' => 45, 'null' => FALSE])
				->addColumn('timestamp', 'integer', ['default' => 0, 'null' => FALSE])
				->addColumn('data', 'text', ['default' => '', 'null' => FALSE])
				->create();
		}
    }


    /**
     * Migrate Up.
     */
    public function up()
    {

    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}