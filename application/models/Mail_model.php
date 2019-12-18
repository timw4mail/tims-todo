<?php

/**
 * Public Model Mail_Model
 * @package Todo
 */
class Mail_model extends CI_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->load->library('email');

		//Set email config
		$config = array(
			'useragent' => "Tim's Todo",
			'protocol' => 'mail',
			'mailtype' => 'text',
			'charset' => 'utf-8',
			'validate' => 'true',
			'priority' => '3',
		);

		$this->email->initialize($config);
	}

	// --------------------------------------------------------------------------

	/**
	 * Check what reminders need to be sent out for the current run
	 *
	 * @return mixed
	 */
	public function check_db()
	{
		//Get the current time
		$now = time();

		//Get two minuts from now
		$interval_new = $now + 900;

		//Get reminders within two minutes of now, that have not been sent
		$this->db->select('reminder.id as rem_id, todo_item.id as task_num,
		 reminder_time, due, sent, title, email, username')
			->from('reminder')
			->join('item', 'todo_item.id = todo_reminder.task_id', 'inner')
			->join('user', 'todo_user.id = todo_reminder.user_id', 'inner')
			->where('reminder_time <', $interval_new)
			->where('sent', 0);

		$query = $this->db->get();

		//If no results, return
		if($query->num_rows() == 0)
			return;

		//Format, then send the email
		$this->_format_email($query);

		//Return debugging info
		$return = $this->email->print_debugger();

		//Log debugging info
		log_message('debug', $return);

		//Clear the email object for the next loop
		$this->email->clear();

		return $return;
	}

	// --------------------------------------------------------------------------

	/**
	 * Format the email to send for a reminder
	 *
	 * @param $query
	 */
	private function _format_email($query)
	{
		foreach($query->result() as $row)
		{
			$due = $row->due;
			$due_reminder = $row->reminder_time;

			//Time until task is due, in seconds
			$until_due = $due - $due_reminder;

			//In hours
			$until_hours = ($until_due >= 3600) ? floor((int)$until_due / 3600) : 0;

			//In additional minutes
			$um = (int)$until_due - ($until_hours * 3600);
			$until_minutes = (int)($um / 60);

			$user = $row->username;
			$task_num = $row->task_num;
			$task = $row->title;
			$to = $row->email;

			$rem_id = $row->rem_id;

			$due_time = date('D M d, Y g:iA T', $due);

			$subject = "Tim's Todo Reminder: '" . $task . "' is due soon";
			$message = $user . ",\r\n".
				"This is a reminder that task #". $task_num .", '".$task."' is due in ".
				$until_hours." hours and ".$until_minutes." minutes, at " . $due_time;

			//Set email parameters
			$this->email->to($to);
			$this->email->from('noreply@timshomepage.net', "Tim's Todo");
			$this->email->message($message);
			$this->email->subject($subject);

			$this->_send($rem_id);

		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Send a reminder, and mark the reminder as sent
	 *
	 * @param $rem_id
	 */
	private function _send($rem_id)
	{
		$result = $this->email->send();

		echo (int) $result . "\n";

		if($result != FALSE)
		{
			//Set as set in the database
			$this->db->set('sent', 1)
				->where('id', $rem_id)
				->update('reminder');
		}
	}
}
// End of models/mail_model.php