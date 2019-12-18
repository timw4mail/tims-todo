<?php

/**
 * Reminder Controller for running via Cron
 */
class Reminder extends MY_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->library('email');
		$this->load->model('mail_model');

	}

	/**
	 * Redirect for snoops
	 */
	public function index()
	{
		//303 Redirect
		$this->todo->redirect_303('task/list');
	}

	/**
	 * Check the database for reminder status
	 */
	public function check_reminder()
	{
		if(!defined('CRON'))
			$this->todo->redirect_303('task/list');
			
		//Do all the fun stuff
		$this->output->set_output($this->mail_model->check_db());
	}
}
// End of controllers/reminder.php