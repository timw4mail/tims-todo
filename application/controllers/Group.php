<?php

/**
 * Group management controller
 */
class Group extends MY_Controller{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->page->set_foot_js_group('js');
		$this->page->set_title('Groups');
	}

	/**
	 * Redirect to group list
	 */
	public function index()
	{
		//303 Redirect
		$this->todo->redirect_303('groups/list');
	}

	/**
	 * List of user's groups
	 */
	public function group_list()
	{
		$data = [
			'group' => $this->todo->get_group_list((int) $this->session->userdata('uid'))
		];
		$this->page->set_title("Group List");
		$this->page->build('friend/group_list', $data);
	}

	/**
	 * Add a new group
	 */
	public function add_sub()
	{
		if($this->input->post('add_sub') != FALSE)
		{
			$this->todo->add_group();

			//Redirect to the group list
			$this->todo->redirect_303('group/manage');
		}
	}

	/**
	 * Delete a group
	 */
	public function del_group()
	{
		$group_id = (int) $this->uri->segment('3');
		$this->output->set_output($this->todo->del_group($group_id));
	}

	/**
	 * Add/Edit a group
	 */
	public function manage($group_id = NULL)
	{
		if(is_null($group_id))
		{
			$this->group_list();
			return;
		}
		
		if($this->input->post('friends'))
		{
			$this->todo->update_group();
		}
		
		$group_id = (int) $group_id;

		$friends_array = array();
		$array = $this->todo->get_friends_in_group($group_id);
		
		foreach($array as $a)
		{
			$friends_array[] = $a['user_id'];
		}

		$data = array();
		$data['group_name'] = $this->todo->get_group_name_by_id($group_id);
		$data['friends'] = $this->todo->get_friend_list();
		$data['selected_friends'] = $friends_array;
		$data['group_perms'] = array();
	
		$this->page->set_title("Manage Group");
		$this->page->build('friend/manage', $data);
	}
}
// End of controllers/group.php