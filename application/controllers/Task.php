<?php

/**
 * Task Controller
 */
class Task extends MY_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('task_model');
		$this->load->library('pagination');
		$this->page->set_title('Tasks');
		$this->page->set_foot_js_group('js');
	}

	// --------------------------------------------------------------------------

	/**
	 * Redirect to task list
	 */
	public function index()
	{
		$this->todo->redirect_303('task/list');
	}

	// --------------------------------------------------------------------------

	/**
	 * List shared tasks
	 */
	public function shared()
	{
		$this->page->set_title("Shared Tasks");
		$tasks = $this->task_model->get_shared_task_list();

		$data = array();
		$data['task_list'] = $tasks;
		$data['list_type'] = "shared";

		$this->page->build('task/list', $data);
	}

	// --------------------------------------------------------------------------

	/**
	 * Get the main task view
	 */
	public function list_tasks()
	{
		$this->page->set_title("View Tasks");
		$tasks = $this->task_model->get_task_list();

		$data = array();
		$data['task_list'] = $tasks;
		$data['list_type'] = 'active';

		$this->page->build('task/list', $data);
	}

	// --------------------------------------------------------------------------

	/**
	 * List archived tasks
	 */
	public function archive($page = 1)
	{
		$per_page = 10;
		$this->page->set_title("Archived Tasks");
		$page = (int) $page;
		$tasks = $this->task_model->get_archived_task_list($page, $per_page);

		// Pagination preferences
		$config = [
			'base_url' => 'https://todo.timshomepage.net/task/archive/',
			'total_rows' => $tasks['num_rows'],
			'per_page' => $per_page,
			'uri_segment' => 3,
			'num_links' => 3,
			'full_tag_open' => '<p id="pagination">',
			'full_tag_close' => '</p>',
			'cur_tag_open' => '<strong>',
			'cur_tag_close' => '</strong>'
		];

		$this->pagination->initialize($config);

		$data = array();
		$data['task_list'] = $tasks;
		$data['list_type'] = 'archived';
		$data['pagination'] = $this->pagination->create_links();

		$this->page->build('task/list', $data);
	}

	// --------------------------------------------------------------------------

	/**
	 * List overdue tasks
	 */
	public function overdue()
	{
		$this->page->set_title("Overdue Tasks");
		$tasks = $this->task_model->get_overdue_task_list();

		$data = array();
		$data['task_list'] = $tasks;
		$data['list_type'] = 'overdue';

		$this->page->build('task/list', $data);
	}

	// --------------------------------------------------------------------------

	/**
	 * Add a task
	 */
	public function add()
	{
		$data = array();
		$data['err'] = '';
		$data['cat_list'] = $this->todo->get_category_select();
		$data['pri_list'] = $this->todo->get_priority_select();
		$data['group_perms'] = '';
		$data['groups'] = $this->todo->get_group_list($this->session->userdata('uid'));
		$data['task_title'] = '';
		$data['description'] = '';
		$data['due'] = mktime(12, 00, 00);
		$data['title'] = '';
		$data['rem_hours'] = 0;
		$data['rem_minutes'] = 30;
		$data['reminder'] = FALSE;
		$data['friends'] = $this->todo->get_friend_list();


		if ($this->input->post('add_sub') == 'Add Task')
		{
			$val = $this->task_model->validate_task();

			if($val === TRUE)
			{
				$done = $this->task_model->add_task();

				if ($done === TRUE)
				{
					//Redirect to task list
					$this->todo->redirect_303('task/list');
				}
				else
				{
					$data['err'][] = "Database Error, Please try again later.";
				}
			}
			else
			{
				//Get form values
				$data = array_merge($data, $this->task_model->form_vals);
				$data['err'] = $val;

			}

		}

		$this->page->set_title("Add Task");
		$this->page->build('task/add', $data);
	}

	// --------------------------------------------------------------------------

	/**
	 * Edit a task
	 *
	 * @param int $task_id
	 */
	public function edit(int $task_id)
	{
		$data = $this->task_model->get_task_by_id($task_id);

		$data['cat_list'] = $this->task_model->get_category_select($task_id);
		$data['pri_list'] = $this->task_model->get_priority_select($task_id);
		$data['stat_list'] = $this->task_model->get_status_select($task_id);
		$data['comments'] = $this->task_model->get_task_comments($task_id);
		$data['groups'] = $this->todo->get_group_list($this->session->userdata('uid'));
		$data['friends'] = $this->todo->get_friend_list();
		$data['checklist'] = $this->task_model->get_checklist($task_id);

		if ($this->input->post('edit_sub') == 'Update Task')
		{
			if($this->task_model->validate_task() === TRUE)
			{
				if ($this->task_model->update_task() === TRUE)
				{
					//Redirect to task list
					$this->session->set_flashdata([
						'message_type' => 'success',
						'message' => 'Task was updated successfully.'
					]);

					$this->todo->redirect_303(site_url('task/list'));
					return;
				}

				$data['err'][] = "Database Error, Please try again later.";
			}
			else
			{
				$data['err'] = $val;
			}
		}

		$this->page->set_title("Edit Task");
		$this->page->build('task/edit', $data);
	}

	// --------------------------------------------------------------------------

	/**
	 * View an individual task
	 *
	 * @param int $task_id
	 */
	public function view(int $task_id = NULL)
	{
		if( ! is_numeric($task_id))
		{
			show_404();
			return;
		}

		$task_id = (int)$task_id;
		$data = $this->task_model->get_task_by_id($task_id);
		$data['comments'] = $this->task_model->get_task_comments($task_id);
		$data['status_id'] = $this->task_model->get_current_status_id($task_id);
		$data['status'] = $this->task_model->get_status_select($task_id, $data['status_id']);
		$data['category'] = $this->task_model->get_category_select($task_id);
		$data['checklist'] = $this->task_model->get_checklist($task_id);
		$data['task'] = $task_id;

		$this->page->set_title("View Task");
		$this->page->set_body_id("task_details");
		$this->page->build('task/view', $data);
	}

	// --------------------------------------------------------------------------

	/**
	 * Delete a task
	 */
	public function delete(int $task_id)
	{
		$this->task_model->delete_task((int) $task_id);
	}

	// --------------------------------------------------------------------------

	/**
	 * Add a task comment
	 */
	public function add_task_comment()
	{
		$res = $this->task_model->add_task_comment($this->input->post('task_id'));
		$this->output->set_output($res);
	}

	// --------------------------------------------------------------------------

	/**
	 * Get a list of comments for the task
	 */
	public function get_task_comments()
	{
		$task_id = (int) $this->input->get('task_id');
		$data = [
			'comments' => $this->task_model->get_task_comments($task_id)
		];
		$this->load->view('task/comments_view', $data);
	}

	// --------------------------------------------------------------------------

	/**
	 * Delete a task comment
	 */
	public function del_task_comment()
	{
		$cid = (int) $this->input->post('comment_id');
		$this->output->set_output($this->task_model->delete_comment($cid));
	}

	// --------------------------------------------------------------------------

	/**
	 * Update the status of a task
	 */
	public function update_status()
	{
		$output = $this->task_model->update_status();
		$this->output->set_output($output);
	}

	// --------------------------------------------------------------------------

	/**
	 * Update the category that the task belongs to.
	 */
	public function update_category()
	{
		$output = $this->task_model->quick_update_category();
		$this->output->set_output($output);
	}

	// --------------------------------------------------------------------------

	/**
	 * Add a checklist item to the task
	 */
	public function add_checklist_item()
	{
		$data = $this->task_model->add_checklist_item();

		if($data == FALSE)
		{
			$this->output->set_output(0);
		}
		else if(is_array($data))
		{
			$this->load->view('task/ajax_checklist', $data);
		}
		else
		{
			$this->output->set_output(-1);
		}
	}

	// --------------------------------------------------------------------------

	/**
	 * Update a task checklist item
	 */
	public function update_checklist_item()
	{
		$check_id = $this->input->post('check_id');
		$checked = $this->input->post('checked');

		$this->output->set_output($this->task_model->update_checklist($check_id, $checked));
	}
}
// End of controllers/task.php