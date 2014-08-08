<?php

/**
 * Friend controller
 */
class Friend extends MY_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('friend_model');

		$this->page->set_title('Friends');
		$this->page->set_foot_js_group('js');
	}

	/**
	 * Alias for friend list
	 */
	public function index()
	{
		$this->friend_list();
		return;
	}

	/**
	 * Get the users's friends
	 */
	public function friend_list()
	{
		$data = array();
		$data['friend_list'] = $this->friend_model->get_friends();
		$this->page->set_title('Friends List');
		$this->page->build('friend/list', $data);
	}

	/**
	 * Friend finder form
	 */
	public function find()
	{
		$data['results'] = null;
		$this->page->set_title('Find Friends');
		$this->page->build('friend/search', $data);
	}

	/**
	 * Send a friend request
	 */
	public function add_request()
	{
		$friend_id = (int) $this->input->post('fid');
		$this->output->set_output($this->friend_model->send_request($friend_id));
	}

	/**
	 * Accept a friend request
	 */
	public function accept_request()
	{
		$aid = xss_clean($this->input->post('aid'));
		$this->output->set_output($this->friend_model->accept_request($aid));
	}

	/**
	 * Reject a friend request
	 */
	public function reject_request()
	{
		$rid = xss_clean($this->input->post('rid'));
		$this->output->set_output($this->friend_model->reject_request($rid));
	}

	/**
	 * Get list of friend requests
	 */
	public function requests()
	{
		$data['request_list'] = $this->friend_model->get_requests();
		$this->page->set_title('Friend Reqests');
		$this->page->build('friend/requests', $data);
	}

	/**
	 * Get results for friend finder
	 */
	public function ajax_search()
	{
		$data['results'] = $this->friend_model->find_friends();
		$this->load->view('friend/ajax_search', $data);
	}
	

}
// End of controllers/friend.php