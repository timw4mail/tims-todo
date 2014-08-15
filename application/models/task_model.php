<?php

/**
 * Public Model Task_Model
 * @package Todo
 */
class Task_model extends CI_Model {

	private $title, $description, $category, $priority, $due, $created,
			$status, $user_id, $task_id, $reminder, $reminder_time,
			$groups, $group_perms, $friends, $friend_perms, $share_type;

	public $form_vals;

	// --------------------------------------------------------------------------

	/**
	 * Get day task list
	 *
	 * Gets tasks for calendar view
	 * @param int $start
	 * @param int $end
	 * @param int $num_days
	 * @return array
	 */
	public function get_day_task_list($start, $end, $num_days)
	{
		$uid = (int) $this->session->userdata('uid');

		//Get user's tasks
		$user_sql = $this->db->select('item.title, item.id, item.due')
			->from('item')
			->where('user_id', $uid)
			->where_not_in('status', [STATUS_COMPLETED, STATUS_CANCELED])
			->where('due >=', $start)
			->where('due <=', $end)
			->get_compiled_select();

		//Get group-shared tasks
		$group_sql = $this->db->select('item.title, item.id, item.due')
			->from('user')
			->join('group_users_link', 'group_users_link.user_id=user.id', 'inner')
			->join('group_task_link', 'group_task_link.group_id=group_users_link.group_id', 'inner')
			->join('item', 'item.id=group_task_link.task_id', 'inner')
			->where('todo_user.id', $uid)
			->where_not_in('status', [STATUS_COMPLETED, STATUS_CANCELED])
			->where('due >=', $start)
			->where('due <=', $end)
			->where('todo_group_task_link.permissions !=', PERM_NO_ACCESS)
			->get_compiled_select();

		//Get friend-shared tasks
		$friend_sql = $this->db->select('item.title, item.id, item.due')
			->from('user')
			->join('user_task_link', 'user_task_link.user_id=user.id', 'inner')
			->join('item', 'item.id=user_task_link.task_id', 'inner')
			->where('todo_user.id', $uid)
			->where_not_in('status', [STATUS_COMPLETED, STATUS_CANCELED])
			->where('due >=', $start)
			->where('due <=', $end)
			->where('todo_user_task_link.permissions !=', PERM_NO_ACCESS)
			->get_compiled_select();

		$sql = "{$user_sql}\nUNION\n{$group_sql}\nUNION\n{$friend_sql}";

		$cal_query = $this->db->query($sql);
		$task_array = $cal_query->result_array();

		//Some loopy variables
		$content = array();
		$due = $start;
		$due_end = $due + 86399;
		$day = 1;

		while($day <= $num_days)
		{
			foreach ($task_array as $task)
			{
				if($task['due'] >= $due && $task['due'] <= $due_end)
				{
					//@codeCoverageIgnoreStart
					if(isset($content[$day]))
					{
						$content[$day] .= '<li><a href="'.site_url('task/view/'.
								$task['id']).'">'.$task['title'].
						'</a><br /> due '.date('h:i A', $task['due']).'</li>';
					}
					//@codeCoverageIgnoreEnd
					else
					{
						$content[$day] = '<li><a href="'.site_url('task/view/'.
								$task['id']).'">'.$task['title'].
						'</a><br /> due '.date('h:i A', $task['due']).'</li>';
					}
				}

			}

			++$day;
			$due += 86400;
			$due_end += 86400;
		}

		return $content;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Checklist
	 *
	 * Returns Checklist for current task
	 * @param int task_id
	 * @return array
	 */
	public function get_checklist($task_id)
	{
		//Get the checklist for the current task from the database
		$chk = $this->db->select('id, task_id, description, is_checked')
			->from('checklist')
			->where('task_id', $task_id)
			->order_by('is_checked', 'asc')
			->order_by('id')
			->get();

		return ($chk->num_rows() > 0) ? $chk->result_array() : array();

	}

	// --------------------------------------------------------------------------

	/**
	 * Add Checklist Item
	 *
	 * Adds a checklist item to the current checklist
	 * @return mixed bool/array
	 */
	public function add_checklist_item()
	{
		$task_id = (int)$this->input->post('task_id');
		$desc = $this->input->post('desc', TRUE);

		//Check if the current item already exists.
		$exists = $this->db->select('task_id, description')
			->from('checklist')
			->where('task_id', $task_id)
			->where('description', $desc)
			->get();

		if($exists->num_rows() < 1)
		{
			//Insert the item
			$this->db->set('task_id', $task_id)
				->set('description', $desc)
				->insert('checklist');

			//Return the row
			$return = $this->db->select('id, task_id, description, is_checked')
				->from('checklist')
				->where('task_id', $task_id)
				->where('description', $desc)
				->get();

			return $return->row_array();
		}

		return FALSE;
	}

	// --------------------------------------------------------------------------

	/**
	 * Delete Comment
	 *
	 * Deletes a comment from a task
	 * @param int $c_id
	 * @return int
	 */
	public function delete_comment($c_id)
	{
		//Get the user group id
		$uid = $this->session->userdata('uid');

		//Delete the comment that matches the c_id and uid
		$this->db->where('id', $c_id)
			->where('user_id', $uid)
			->delete('item_comments');

		if($this->db->affected_rows() > 0)
		{
			return $this->db->affected_rows();
		}
		else
		{
			return -1;
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Task List
	 *
	 * Retrieves the user's tasks from the database
	 * @return mixed
	 */
	public function get_task_list()
	{
		$this->db->from('todo_task_view')
			->where('user_id', (int) $this->session->userdata('uid'))
			->where_not_in('status_id', [STATUS_COMPLETED, STATUS_CANCELED]);

		$res = $this->db->get();

		if($res->num_rows()==0) return;

		$result_array = array();
		$i=1;
		foreach($res->result_array() as $row)
		{
			$result_array[$i] = $row;
			$result_array[$i]['overdue'] = ($row['due'] < time() && $row['due'] != 0 || $row['priority'] == "Immediate");
			$i++;
		}

		return $result_array;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get archived task list
	 *
	 * Retrieves the user's archived tasks from the database
	 *
	 * @param int $page
	 * @param int $per_page
	 * @return array
	 */
	public function get_archived_task_list($page=0, $per_page=10)
	{
		$offset = ($page == 1) ? 0 : $page;
		$limit = $per_page;

		// Get the number of tasks for pagination
		$this->db->select('item.id, user_id, category_id')
			->from('item')
			->where('user_id', $this->session->userdata('uid'))
			->where_in('status', [STATUS_COMPLETED, STATUS_CANCELED])
			->order_by('modified', 'desc');

		$r_rows = $this->db->get();

		$this->db->from('todo_task_view')
			->where('user_id', $this->session->userdata('uid'))
			->where_in('status_id', [STATUS_COMPLETED, STATUS_CANCELED])
			->order_by('modified', 'desc')
			->limit($limit, $offset);

		$res = $this->db->get();

		if($res->num_rows()==0)
			return;

		$result_array = array();
		$i=1;
		foreach($res->result_array() as $row)
		{
			$result_array[$i] = $row;
			$result_array[$i]['overdue'] = FALSE;
			$i++;
		}

		$result_array['num_rows'] = $r_rows->num_rows();

		return $result_array;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get overdue task list
	 *
	 * Retrieves the user's overdue tasks from the database
	 * @return array
	 */
	public function get_overdue_task_list()
	{
		$this->db->select('item.id, user_id, category_id, item.priority,
			status, item.title, due, modified, created,
			category.title as category, priority.value as priority,
			status.value as status')
			->from('item')
			->join('category', 'category.id=item.category_id', 'inner')
			->join('priority', 'priority.id=item.priority', 'inner')
			->join('status', 'status.id=item.status', 'inner')
			->where('user_id', (int)$this->session->userdata('uid'))

			->group_start()
			->where('due <', time())
			->or_where('item.priority', 9)
			->group_end()

			->where_not_in('status', [STATUS_COMPLETED, STATUS_CANCELED])
			->where('due !=', 0)
			->order_by('due', 'asc')
			->order_by('item.priority', 'desc');

		$res = $this->db->get();

		if($res->num_rows()==0)
			return;

		$result_array = array();
		$i=1;
		foreach($res->result_array() as $row)
		{
			$result_array[$i] = $row;

			// Overdue is set as false to cut down on visual noise.
			// Since every task in the list is overdue, using the 
			// visual style is redundant
			$result_array[$i]['overdue'] = FALSE;
			$i++;
		}

		return $result_array;
	}

	/**
	 * Get shared task list
	 *
	 * returns a list of shared tasks
	 * @return array
	 */
	public function get_shared_task_list()
	{
		$user_id = (int) $this->session->userdata('uid');

		$user_shared_sql = $this->db->select('item.id, user.id as user_id, category_id, item.priority,
			status, item.title, due, modified, created,
			category.title as category, priority.value as priority,
			status.value as status, group_task_link.permissions as group_perms')
			->distinct()
			->from('user')
			->join('group_users_link', 'group_users_link.user_id=user.id', 'inner')
			->join('group_task_link', 'group_task_link.group_id=group_users_link.group_id', 'inner')
			->join('item', 'item.id=group_task_link.task_id', 'inner')
			->join('category', 'category.id=item.category_id', 'inner')
			->join('priority', 'priority.id=item.priority', 'inner')
			->join('status', 'status.id=item.status', 'inner')
			->where('todo_user.id', $user_id)
			->where_not_in('status', [STATUS_COMPLETED, STATUS_CANCELED])
			->where('todo_group_task_link.permissions !=', PERM_NO_ACCESS)
			->get_compiled_select();

		$group_shared_sql = $this->db->select('item.id, user.id as user_id, category_id, item.priority,
			status, item.title, due, modified, created,
			category.title as category, priority.value as priority,
			status.value as status, user_task_link.permissions as user_perms')
			->distinct()
			->from('user')
			->join('user_task_link', 'user_task_link.user_id=user.id', 'inner')
			->join('item', 'item.id=user_task_link.task_id', 'inner')
			->join('category', 'category.id=item.category_id', 'inner')
			->join('priority', 'priority.id=item.priority', 'inner')
			->join('status', 'status.id=item.status', 'inner')
			->where('todo_user.id', $user_id)
			->where_not_in('status', [STATUS_COMPLETED, STATUS_CANCELED])
			->where('todo_user_task_link.permissions !=', PERM_NO_ACCESS)
			->get_compiled_select();

		$sql = "{$user_shared_sql}\nUNION ALL\n{$group_shared_sql}";

		$res = $this->db->query($sql);

		$now = time();

		$result_array = array();
		$i=1;
		foreach($res->result_array() as $row)
		{
			$result_array[$i] = $row;
			$result_array[$i]['overdue'] = ($result_array[$i]['due'] < $now && $result_array[$i]['due'] != 0);
			$i++;
		}

		return $result_array;
	}

	// --------------------------------------------------------------------------

	/**
	 * Validate Task
	 *
	 * Validates a new task before database submission.
	 * @return mixed
	 */
	public function validate_task()
	{
		// Clear previous validations
		$this->form_vals = NULL;

		$due = $this->input->post('due', TRUE);
		$due_hour = $this->input->post('due_hour', TRUE);
		$due_minute = $this->input->post('due_minute', TRUE);

		$err = array();

		// Basic validation
		$valid = $this->form_validation->run('task');

		if ( ! $valid)
		{
			$err = array_merge($err, $this->form_validation->get_error_array());
		}

		//Check due date
		if ($due != 0)
		{
			//Verify date format
			$valid = $this->validation_callbacks->due_date($due);

			if ( ! $valid)
			{
				return $err;
			}

			$due_a = explode('-', $due);
			$min = $due_minute;
			$hour = $due_hour;

			$due_timestamp = mktime($hour,$min,0,$due_a[1],$due_a[2],$due_a[0]);

			//Return form values
			$this->form_vals['due'] = $due_timestamp;
			$this->form_vals['due_minute'] = $due_minute;
		}
		else
		{
			$due_timestamp = 0;
		}

		//If there is an email reminder
		if($this->input->post('reminder') == 'rem_true')
		{
			if($due == 0)
			{
				$err[] = "You must set a due date in order to get a reminder.";
			}

			if(!is_numeric($this->input->post('rem_hours')) OR
			!is_numeric($this->input->post('rem_minutes')))
			{
				$err[] = "You must put numeric hours and minutes for a reminder time.";
			}
			else
			{
				$reminder_hour = (int)$this->input->post('rem_hours');
				$reminder_min = (int)$this->input->post('rem_minutes');

				$seconds = ($reminder_hour * 3600)+($reminder_min * 60);
				$reminder_time = $due_timestamp - $seconds;

				$this->reminder = TRUE;
				$this->reminder_time = $reminder_time;

				//Return form values
				$this->form_vals['reminder'] = TRUE;
				$this->form_vals['rem_hours'] = $reminder_hour;
				$this->form_vals['rem_minutes'] = $reminder_min;
			}
		}
		else
		{
			$this->reminder = FALSE;
		}

		$share_type = FALSE;

		//If the task is shared
		if($this->input->post('share') !== FALSE)
		{
			$groups = $this->input->post('group', TRUE);
			$group_perms = $this->input->post('group_perms', TRUE);
			$friends = $this->input->post('friend', TRUE);
			$friend_perms = $this->input->post('friend_perms', TRUE);

			if($groups != FALSE && $group_perms != FALSE)
			{
				$share_type = 'group';
			}

			if($friends != FALSE && $friend_perms != FALSE)
			{
				$share_type = 'friend';
			}

		}

		//If there aren't any errors
		if(empty($err))
		{
			$this->groups = ( ! empty($groups)) ? $groups : FALSE;
			$this->friends = ( ! empty($friends)) ? $friends : FALSE;
			$this->share_type = $share_type;
			$this->due = $due_timestamp;
			$this->friend_perms = (isset($friend_perms)) ? $friend_perms : FALSE;
			$this->group_perms = (isset($group_perms)) ? $group_perms : FALSE;
			$this->user_id = $this->session->userdata('uid');
			$this->task_id = ($this->input->post('task_id') != FALSE) ?
					$this->input->post('task_id') :
					$this->db->count_all('item') + 1;

			return TRUE;
		}
		else //otherwise, return the errors
		{
			return $err;
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Add Task
	 *
	 * Submits new task to database
	 * @return bool
	 */
	public function add_task()
	{
		$title = $this->input->post('title', TRUE);
		$desc = $this->input->post('desc', TRUE);
		$category = (int) $this->input->post('category');
		$priority = (int) $this->input->post('priority');
		$status = ($this->input->post('status') == FALSE) ? 1 : $this->input->post('status');
		$created = time();

		/*$title = $this->title;
		$desc = $this->description;
		$category = $this->category;
		$priority = $this->priority;
		$status = $this->status;
		$created = $this->created;*/
		$due = $this->due;
		$uid = $this->user_id;

		$this->db->set('user_id', $uid)
			->set('category_id', $category)
			->set('priority', $priority)
			->set('status', $status)
			->set('title', $title)
			->set('description', $desc)
			->set('due', $due)
			->set('created', $created)
			->set('modified', 0);

		$this->db->insert('item');

		if($this->db->affected_rows() < 1)
			return FALSE;

		//Get last inserted task
		$query = $this->db->select('max(id) as id')->from('item')->get();
			$row = $query->row();
			$task_id = $row->id;

		//Get groups
		if($this->groups != FALSE)
		{
			if($this->group_perms != FALSE)
			{
				foreach($this->groups as $group)
				{
					$this->db->set('group_id', $group)
						->set('task_id', $task_id)
						->set('permissions', $this->group_perms)
						->insert('group_task_link');
				}
			}
		}

		//Get friends
		if($this->friends != FALSE)
		{
			if($this->friend_perms != FALSE)
			{
				foreach($this->friends as $friend)
				{
					$this->db->set('user_id', $friend)
						->set('task_id', $task_id)
						->set('permissions', $this->friend_perms)
						->insert('user_task_link');
				}
			}
		}


		if($this->reminder == TRUE)
		{
			$reminder_time = $this->reminder_time;
			$this->_add_reminder($task_id, $reminder_time);
		}

		return TRUE;

	}

	// --------------------------------------------------------------------------

	/**
	 * Update Task
	 *
	 * Updates current task
	 * @return bool
	 */
	public function update_task()
	{
		$title = $this->title;
		$desc = str_replace('<br>', '<br />', $this->description);
		$category = $this->category;
		$priority = $this->priority;
		$status = $this->status;
		$due = $this->due;
		$uid = $this->user_id;
		$task_id = $this->task_id;

		$this->db->set('category_id', $category)
			->set('priority', $priority)
			->set('status', $status)
			->set('title', $title)
			->set('description', $desc)
			->set('due', $due)
			->set('modified', time())
			->where('id', $task_id)
			->where('user_id', $uid);

		$this->db->update('item');

		//Check the status separately, to account for email reminders
		$this->update_status();

		if($this->reminder == TRUE)
		{
			$reminder_time = $this->reminder_time;
			$this->_add_reminder($task_id, $reminder_time);
		}
		else
		{
			// Delete old reminders
			$this->db->where('task_id', $task_id)
				->delete('reminder');
		}

		// Remove old shared permissions
		{
			// Delete existing groups and users
			$group_list = $this->_get_task_groups($task_id);

			// Delete groups
			if ( ! empty($group_list))
			{
				$this->db->where_in('group_id', $group_list)
					->where('task_id', $task_id)
					->delete('group_task_link');
			}

			// Delete friends
			$friend_list = $this->_get_task_users($task_id);

			if ( ! empty($friend_list))
			{
				$this->db->where_in('user_id', $friend_list)
					->where('task_id', $task_id)
					->or_where('user_id', (int) $this->session->userdata('uid'))
					->where('task_id', $task_id)
					->delete('user_task_link');
			}

		}


		//Get groups
		if($this->share_type == 'group')
		{
			if($this->group_perms !== FALSE)
			{
				foreach($this->groups as $group)
				{
					$this->db->set('group_id', $group)
						->set('task_id', $task_id)
						->set('permissions', $this->group_perms)
						->insert('group_task_link');
				}
			}
		}

		//Get friends
		if($this->share_type == 'friend')
		{
			if($this->friend_perms !== FALSE)
			{
				foreach($this->friends as $friend)
				{
					$this->db->set('user_id', $friend)
						->set('task_id', $task_id)
						->set('permissions', $this->friend_perms)
						->insert('user_task_link');
				}

				if ($this->db->affected_rows() < 1)
						{return false;}

				//Set current user too
				$this->db->set('user_id', $this->session->userdata('uid'))
					->set('task_id', $task_id)
					->set('permissions', $this->friend_perms)
					->insert('user_task_link');
			}
		}

		return true;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Task By Id
	 *
	 * Retreives task from database by task id
	 * @param int $task_id
	 * @return array
	 */
	public function get_task_by_id($task_id)
	{
		//Get the task
		$task = $this->db->select(
				'item.id,
				item.user_id,
				item.priority,
				item.title,
				item.due,
				item.modified,
				item.created,
				item.description,
				user.username,
				status.value as current_status,
				priority.value as priority,
				category.title as cat_name'
		)
		->from('item')
		->join('user', 'user.id=todo_item.user_id', 'inner')
		->join('category', 'category.id=todo_item.category_id', 'inner')
		->join('priority', 'priority.id=todo_item.priority', 'inner')
		->join('status', 'status.id=todo_item.status', 'inner')
		->where('todo_item.id', (int) $task_id)
		->get();

		$task_array = $task->row_array();

		//Get the task permissions
		$result_array = array_merge($task_array, $this->_get_task_perms($task_id));

		//Get selected groups
		$result_array['selected_groups'] = $this->_get_task_groups($task_id);

		//Get selected friends
		$result_array['selected_friends'] = $this->_get_task_users($task_id);

		//Get any related task reminders
		$query2 = $this->db->select('task_id, reminder_time')
			->from('reminder')
			->where('task_id', $task_id)
			->where('user_id', $this->session->userdata('uid'))
			->get();

		//If there aren't any reminders
		if($query2->num_rows() < 1)
		{
			$result_array['reminder'] = FALSE;
			$result_array['rem_hours'] = 0;
			$result_array['rem_minutes'] = 30;
			return $result_array;
		}
		else //There are reminders
		{
			$res2 = $query2->row();

			$result_array['reminder'] = TRUE;

			//Time until task is due, in seconds
			$until_due = $result_array['due'] - $res2->reminder_time;

			//In hours
			$until_hours = ($until_due >= 3600) ? floor((int)$until_due / 3600) : 0;

			//In additional minutes
			$until_seconds = (int)$until_due - ($until_hours * 3600);
			$until_minutes = (int)($until_seconds / 60);

			$result_array['rem_hours'] = $until_hours;
			$result_array['rem_minutes'] = $until_minutes;

			return $result_array;
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Get the status id for the selected task
	 *
	 * @param int $task_id
	 * @return int
	 */
	public function get_current_status_id($task_id=0)
	{
		// @codeCoverageIgnoreStart
		if($task_id==0)
		{
			$task_id=$this->uri->segment($this->uri->total_segments());
		}
		// @codeCoverageIgnoreEnd

		//Get the status from the task
		$task = $this->db->select('id, status')
			->from('item')
			->where('id', (int) $task_id)
			->get();

		$trow = $task->row();
		$status_id = $trow->status;

		return $status_id;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Status Select
	 *
	 * Returns select options for task status
	 * @param int $task_id
	 * @param int $status_id
	 * @return string
	 */
	public function get_status_select($task_id=0, $status_id=NULL)
	{
		$html = '';

		if (is_null($status_id))
		{
			$status_id = $this->get_current_status_id($task_id);
		}

		//Get the list of statuses
		$query = $this->db->select('id, value as desc')
				->from('status')
				->order_by('id')
				->get();

		foreach($query->result() as $row)
		{
			$html .= T5.'<option value="'.$row->id.'"';
			//Mark the appropriate one selected
			$html .= ($row->id == $status_id) ? ' selected="selected">': '>';
			$html .= $row->desc;
			$html .= '</option>'.NL;
		}

		return $html;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Priority Select
	 *
	 * Returns priority options for task status
	 * @param int $task_id
	 * @return string
	 */
	public function get_priority_select($task_id=0)
	{
		// @codeCoverageIgnoreStart
		if($task_id==0)
			$task_id=$this->uri->segment($this->uri->total_segments());
		// @codeCoverageIgnoreEnd

		$html = '';

		//Get the status from the task
		$task = $this->db->select('id, priority')
				->from('item')
				->where('id', $task_id)
				->order_by('id', 'asc')
				->get();

		$trow = $task->row();
		$pri_id = $trow->priority;

		//Get the list of statuses
		$query = $this->db->select('id, value as desc')
				->from('priority')
				->get();

		foreach($query->result() as $row)
		{
			$html .= T5.'<option value="'.$row->id.'"';
			//Mark the appropriate one selected
			$html .= ($row->id == $pri_id) ? ' selected="selected">': '>';
			$html .= $row->desc;
			$html .= '</option>'.NL;
		}

		return $html;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Category Select
	 *
	 * Returns category options for task status
	 * @param int $id
	 * @return string
	 */
	public function get_category_select($task_id=0)
	{
		// @codeCoverageIgnoreStart
		if($task_id==0)
			$task_id=$this->uri->segment($this->uri->total_segments());
		// @codeCoverageIgnoreEnd

		$html = '';

		//Get the user's category group
		$user_group_id = $this->todo->get_user_group();

		//Get the category from the task
		$task = $this->db->select('id, category_id')
				->from('item')
				->where('id', $task_id)
				->get();

		$trow = $task->row();
		$category_id = $trow->category_id;

		//Get the list of categories
		$query = $this->db->select('id, title as desc')
				->from('category')
				->where('group_id', 0)
				->or_where('group_id', $user_group_id)
				->order_by('title', 'asc')
				->get();

		foreach($query->result() as $row)
		{
			$html .= T5.'<option value="'.$row->id.'"';
			//Mark the appropriate one selected
			$html .= ($row->id == $category_id) ? ' selected="selected">': '>';
			$html .= $row->desc;
			$html .= '</option>'.NL;
		}

		return $html;
	}

	// --------------------------------------------------------------------------

	/**
	 * Update Status
	 *
	 * Updates task status
	 * @return int
	 */
	public function update_status()
	{
		$new_status = (int)$this->input->post('status');
		$task_id = (int)$this->input->post('task_id');

		//If you are marking it as complete
		if($new_status == STATUS_COMPLETED)
		{
			//Check for reminders attached to that task
			$rem_q = $this->db->select('id')
				->from('reminder')
				->where('task_id', $task_id)
				->where('sent', 0)
				->get();

			//If there are reminders attached
			if($rem_q->num_rows() > 0)
			{
				//Go through the results, and mark each as done
				foreach($rem_q->result() as $reminder)
				{
					$this->db->set('sent', 1)
						->where('id', $reminder->id)
						->update('reminder');

				}
			}
		}
		else //Maybe it wasn't really complete yet
		{
			//Check if the task was marked complete
			$stat_q = $this->db->select('status')
				->from('item')
				->where('id', $task_id)
				->where_in('status', [STATUS_COMPLETED, STATUS_CANCELED])
				->get();

			//if it was complete, check for associated reminders that are due in the future
			if($stat_q->num_rows() > 0)
			{
				$now = time();

				$rem_q = $this->db->select('id')
					->from('reminder')
					->where('task_id', $task_id)
					->where('reminder_time >', $now)
					->get();

				//Update those reminders to be sent
				foreach($rem_q->result() as $reminder)
				{
					$this->db->set('sent', 0)
						->where('id', $reminder->id)
						->update('reminder');
				}
			}
		}

		//I guess we should actually update the status
		$this->db->set('status', $new_status)
				->set('modified', time())
				->where('id', $task_id)
				->update('item');

		return $this->db->affected_rows(); //'success';
	}

	// --------------------------------------------------------------------------

	/**
	 * Quick Update Category
	 *
	 * Updates task category via ajax
	 * @return int
	 */
	public function quick_update_category()
	{
		$new_category = (int)$this->input->post('category');
		$task_id = (int)$this->input->post('task_id');

		$this->db->set('category_id', $new_category)
				->where('id', $task_id)
				->update('item');

		return $this->db->affected_rows(); //'success';
	}

	// --------------------------------------------------------------------------

	/**
	 * Get task Comments
	 *
	 * Returns comments for the current task
	 * @param int $task_id
	 * @return array
	 */
	public function get_task_comments($task_id)
	{
		$comment_q = $this->db->select('item_comments.id, user_id, item_id, comment, time_posted, email, status.value as status')
				->from('item_comments')
				->join('user', 'item_comments.user_id=user.id', 'inner')
				->join('status', 'item_comments.status=status.id', 'inner')
				->where('item_id', (int) $task_id)
				->order_by('time_posted', 'desc')
				->get();

		$result_array = $comment_q->result_array();
		return $result_array;
	}

	// --------------------------------------------------------------------------

	/**
	 * Add task comment
	 *
	 * Adds a task comment to the database
	 * @param int $task_id
	 * @return string
	 */
	public function add_task_comment($task_id)
	{
		$user_id = $this->session->userdata('uid');
		$comment = xss_clean($this->input->post('comment'));
		$status = $this->input->post('status');
		$time = time();

		//Insert the comment
		$this->db->set('item_id', $task_id)
				->set('user_id', $user_id)
				->set('comment', $comment)
				->set('time_posted', $time)
				->set('status', $status)
				->insert('item_comments');

		return $this->db->affected_rows();
	}

	// --------------------------------------------------------------------------

	/**
	 * Delete Task
	 *
	 * Checks for permissions to delete a task
	 * @param int $task_id
	 * @return null
	 */
	public function delete_task($task_id)
	{
		$user = $this->session->userdata('uid');

		//Check if the user is task admin
		$user_perms = $this->db->select('item.id')
			->from('item')
			->where('user_id', $user)
			->get();

		$admin = ($user_perms->num_rows() > 0) ? TRUE : FALSE;

		//Check if the user has permission to delete this task
		$friend_perms = $this->db->select('user.id')
			->distinct()
			->from('user')
			->join('user_task_link', 'user_task_link.user_id=user.id', 'inner')
			->join('item', 'user_task_link.task_id=item.id', 'inner')
			->where('user.id', $user)
			->where('task_id', $task_id)
			->where('permissions', 9)
			->get();

		$user_admin = ($friend_perms->num_rows() > 0) ? TRUE : FALSE;


		//Check if the group this user is in has permission to delete this task
		$group_perms = $this->db->select('user.id')
			->distinct()
			->from('user')
			->join('group_users_link', 'group_users_link.user_id=user.id', 'inner')
			->join('group_task_link', 'group_task_link.group_id=group_users_link.group_id', 'inner')
			->where('user.id', $user)
			->where('group_task_link.task_id', $task_id)
			->where('permissions', 9)
			->get();

		$group_admin = ($group_perms->num_rows() > 0) ? TRUE : FALSE;


		//Check if the user has permission
		if($admin === TRUE)
		{
			$this->_remove_task($task_id);
			return;
		}
		else if($user_admin === TRUE)
		{
			$this->_remove_task($task_id);
			return;
		}
		else if($group_admin === TRUE)
		{
			$this->_remove_task($task_id);
			return;
		}
		else
		{
			show_error('You do not have permission to delete this task.');
			return;
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Update Checklist
	 *
	 * Updates a checklist
	 *
	 * @param int $check_id
	 * @param bit $checked
	 */
	public function update_checklist($check_id,  $checked)
	{
		$task_id = $this->input->post('task_id');

		//Get the task checklist items
		$clq = $this->db->select('is_checked')
			->from('checklist')
			->where('task_id', $task_id)
			->get();

		$checklist = $clq->result_array();

		$num_items = count($checklist);
		$num_checked = 0;

		//Count the number checked
		foreach($checklist as $bit)
		{
			//if true, add 1, if false, add 0;
			$num_checked += $bit['is_checked'];
		}

		$unchecked = $num_items - $num_checked;

		if($checked == 1) //Checking another box
		{
			//Check if it's the first checkbox to be checked
			$is_first = ($num_checked == 0) ? TRUE : FALSE;

			//Check if it's the last checkbox to be checked
			$is_last = ($unchecked == 1) ? TRUE : FALSE;

			//Update the checklist item in db
			$this->db->set('is_checked', 1)
				->where('id', $check_id)
				->update('checklist');

			//if the checkbox doesn't update, show error
			if($this->db->affected_rows() < 1)
			{
				return -1;
			}

			//If it's the first item, set the status of the task to "In progress"
			if($is_first == TRUE)
			{
				$this->db->set('status', 3)
					->where('id', $task_id)
					->update('item');

				return ($this->db->affected_rows() > 0) ? "first" : -1;
			}

			if($is_last == TRUE) //set status to "Completed"
			{
				$this->db->set('status', 2)
					->where('id', $task_id)
					->update('item');

				return ($this->db->affected_rows() > 0) ? "last" : -1;
			}
			else
			{
				return 1;
			}

		}
		else if($checked == 0) //Unchecking a checkbox
		{
			$is_last = ($unchecked == 0) ? TRUE : FALSE;

			//Update the checklist item in db
			$this->db->set('is_checked', 0)
				->where('id', $check_id)
				->update('checklist');

			if($this->db->affected_rows() < 1)
				return PERM_NO_ACCESS;

			//if unchecking the last item, set status as "In progress"
			if($is_last == TRUE)
			{
				$this->db->set('status', 3)
					->where('id', $task_id)
					->update('item');

				return ($this->db->affected_rows() > 0) ? "first" : -1;
			}
		}
	}


	// --------------------------------------------------------------------------

	/**
	 * Add Reminder
	 *
	 * Adds reminder to the database
	 */
	private function _add_reminder($task_id, $reminder_time)
	{
		$user_id = (int) $this->session->userdata('uid');

		//Check for a reminder with the current task id
		$query = $this->db->select('task_id')
			->from('reminder')
			->where('task_id', $task_id)
			->get();

		//Check if there is an existing reminder for this task
		if($query->num_rows() < 1)
		{
			$this->db->set('task_id', $task_id)
				->set('reminder_time', $reminder_time)
				->set('user_id', $user_id)
				->insert('reminder');
		}
		else //If there is, update it.
		{

			$this->db->set('reminder_time', $reminder_time)
				->where('task_id', $task_id)
				->where('user_id', $user_id)
				->update('reminder');
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Task Groups
	 *
	 * Returns groups related to the current task
	 * @param int $task_id
	 * @return array
	 */
	private function _get_task_groups($task_id)
	{
		$groups = $this->db->select('group_id')
			->from('group_task_link')
			->where('permissions !=', PERM_NO_ACCESS)
			->where('task_id', $task_id)
			->get();

		$group_list = $groups->result_array();
		$result_array = array();

		if($groups->num_rows() < 1)
		{
			return FALSE;
		}

		foreach($group_list as $group)
		{
			$result_array[] = $group['group_id'];
		}

		return $result_array;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Task Users
	 *
	 * Returns users related to the current task
	 * @param int $task_id
	 * @return array
	 */
	private function _get_task_users($task_id)
	{
		$query = $this->db->select('user_id')
			->from('user_task_link')
			->where('permissions !=', PERM_NO_ACCESS)
			->where('task_id', $task_id)
			->get();

		$friend_list = $query->result_array();
		$result_array = array();

		if($query->num_rows() < 1)
		{
			return FALSE;
		}

		foreach($friend_list as $friend)
		{
			$result_array[] = $friend['user_id'];
		}

		return $result_array;
	}

	// --------------------------------------------------------------------------

	/**
	 * Get Task Perms
	 *
	 * Get the permissions of the current task
	 * @param int $id
	 * @return array
	 */
	private function _get_task_perms($task_id)
	{
		/**
		 * Get the task shared permissions
		 */

		//Get groups associated with the task and user
		$group_perms = $this->db->select('group_task_link.permissions')
			->from('user')
			->join('group_users_link', 'group_users_link.user_id=user.id', 'inner')
			->join('group_task_link', 'group_task_link.group_id=group_users_link.group_id', 'inner')
			->join('item', 'item.id=group_task_link.task_id', 'inner')
			->where('todo_item.id', (int) $task_id)
			->where('todo_group_task_link.permissions !=', PERM_NO_ACCESS)
			->where('todo_user.id', (int) $this->session->userdata('uid'))
			->limit(1)
			->get();

		//Get friends associated with the task and user
		$friend_perms = $this->db->select('user_task_link.permissions')
			->from('item')
			->join('user_task_link', 'user_task_link.task_id=item.id')
			->where('todo_user_task_link.permissions !=', PERM_NO_ACCESS)
			->where('todo_user_task_link.task_id', (int) $task_id)
			->where('todo_user_task_link.user_id', (int) $this->session->userdata('uid'))
			->limit(1)
			->get();

		//Set permissions to no access as default
		$result_array = array(
			'friend_perms' => PERM_NO_ACCESS,
			'group_perms' => PERM_NO_ACCESS,
			'user_perms' => PERM_NO_ACCESS
		);

		$resf = ($friend_perms->num_rows() == 1) ?  $friend_perms->row_array() : array('permissions' => FALSE);
		$resg = ($group_perms->num_rows() == 1) ? $group_perms->row_array() : array('permissions' => FALSE);

		//Group permissions are set
		if($resg['permissions'] !== FALSE && $resf['permissions'] === FALSE)
		{
			//Return groups query
			$result_array['group_perms'] = $resg['permissions'];
			$result_array['friend_perms'] = PERM_NO_ACCESS;

		}

		//Group and friend permissions set
		if($resg['permissions'] !== FALSE && $resf['permissions'] !== FALSE)
		{
			//Return groups query and friend_perms
			$result_array['friend_perms'] = $resf['permissions'];
			$result_array['group_perms'] = $resf['permissions'];

		}

		//Friend Permissions are set
		if($resg['permissions'] === FALSE && $resf['permissions'] !== FALSE)
		{
			//Return user query
			$result_array['friend_perms'] = $resf['permissions'];
			$result_array['group_perms'] = PERM_NO_ACCESS;
		}

		/**
		 * Get the current user's permissions from the database
		 */

		//Check group permissions
		$upG = $this->db->select('permissions')
			->from('user')
			->join('group_users_link', 'group_users_link.user_id=user.id', 'inner')
			->join('group_task_link', 'group_task_link.group_id=group_users_link.group_id', 'inner')
			->where('todo_group_users_link.user_id', (int) $this->session->userdata('uid'))
			->where('todo_group_task_link.task_id', (int) $task_id)
			->get();

		//Check user permissions
		$upU = $this->db->select('permissions')
			->from('user_task_link')
			->where('todo_user_task_link.user_id', (int) $this->session->userdata('uid'))
			->where('todo_user_task_link.task_id', $task_id)
			->get();

		//Check if task admin
		$upA = $this->db->select('id')
			->from('item')
			->where('id', (int) $task_id)
			->where('user_id', (int) $this->session->userdata('uid'))
			->get();

		//Check for admin permissions
		if($upA->num_rows() > 0)
		{
			$result_array['user_perms'] = 9;
			return $result_array;
		}
		else //User is not admin
		{
			//Check group permissions
			if($upG->num_rows() > 0)
			{
				$upG_row = $upG->row_array();
				$result_array['user_perms'] = $upG_row['permissions'];
			}

			//Check individual user permissions
			if($upU->num_rows() > 0)
			{
				$up_row = $upU->row_array();
				$result_array['user_perms'] = $up_row['permissions'];
			}

			//Determine whether the current user can view and/or edit this task
			if($result_array['user_perms'] == PERM_NO_ACCESS)
			{
				show_error('You do not have permission to view this task.');
				return;
			}
			else if($result_array['user_perms'] < PERM_WRITE_ACCESS && $this->uri->segment('2') == "edit")
			{
				show_error('You do not have permission to edit this task.');
				return;
			}

			return $result_array;
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Remove Task
	 *
	 * Delete a task from the database
	 * @param int $task_id
	 */
	private function _remove_task($task_id)
	{
		//Delete references from reminder
		$this->db->where('task_id', $task_id)
			->delete('reminder');

		//Delete references from group_task_link
		$this->db->where('task_id', $task_id)
			->delete('group_task_link');

		//Delete references from user_task_link
		$this->db->where('task_id', $task_id)
			->delete('user_task_link');

		//Delete task comments
		$this->db->where('item_id', $task_id)
			->delete('item_comments');

		//Delete checklists
		$this->db->where('task_id', $task_id)
			->delete('checklist');

		//Delete the task
		$this->db->where('id', $task_id)
			->delete('item');

		//Redirect to the task list
		$this->todo->redirect_303(site_url('task/list'));
	}
}